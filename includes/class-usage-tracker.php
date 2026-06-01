<?php
/**
 * Usage Tracker
 *
 * Tracks OpenAI API token consumption per tool and estimates costs based on
 * the currently selected model's pricing.
 *
 * @since      1.0.0
 * @package    Adverto_Master
 * @subpackage Adverto_Master/includes
 */

class Adverto_Usage_Tracker {

    /**
     * Supported OpenAI models with pricing per 1 million tokens (USD).
     *
     * 'input'  — cost per 1M prompt tokens
     * 'output' — cost per 1M completion tokens
     * 'vision' — whether the model supports image input
     */
    const MODELS = [
        'gpt-4o-mini'  => [
            'name'        => 'GPT-4o mini',
            'input'       => 0.15,
            'output'      => 0.60,
            'vision'      => true,
            'description' => 'Fastest & cheapest. Great for bulk alt text and simple SEO tasks.',
        ],
        'gpt-4.1-mini' => [
            'name'        => 'GPT-4.1 mini',
            'input'       => 0.40,
            'output'      => 1.60,
            'vision'      => false,
            'description' => 'Balanced speed and quality for SEO generation.',
        ],
        'gpt-4o'       => [
            'name'        => 'GPT-4o',
            'input'       => 2.50,
            'output'      => 10.00,
            'vision'      => true,
            'description' => 'High quality output. Best for important pages. Vision-capable.',
        ],
        'gpt-4.1'      => [
            'name'        => 'GPT-4.1',
            'input'       => 2.00,
            'output'      => 8.00,
            'vision'      => false,
            'description' => 'Latest reasoning model. Best quality for complex SEO tasks.',
        ],
    ];

    /**
     * The wp_options key used to store running token totals.
     */
    const OPTION_KEY = 'adverto_token_usage';

    /**
     * Return the model ID currently selected in the plugin settings.
     *
     * Falls back to 'gpt-4o-mini' when no value is stored.
     *
     * @return string Model ID.
     */
    public static function get_selected_model() {
        $settings = get_option( 'adverto_master_settings', array() );
        $model    = isset( $settings['openai_model'] ) ? $settings['openai_model'] : '';

        if ( empty( $model ) || ! array_key_exists( $model, self::MODELS ) ) {
            return 'gpt-4o-mini';
        }

        return $model;
    }

    /**
     * Return the info array for a given model ID.
     *
     * Falls back to gpt-4o-mini when the requested model is not found.
     *
     * @param  string $model_id OpenAI model identifier.
     * @return array  Model info array containing name, input/output pricing, vision, and description.
     */
    public static function get_model_info( $model_id ) {
        if ( array_key_exists( $model_id, self::MODELS ) ) {
            return self::MODELS[ $model_id ];
        }

        return self::MODELS['gpt-4o-mini'];
    }

    /**
     * Return the full MODELS constant array.
     *
     * @return array All available models with their metadata.
     */
    public static function get_available_models() {
        return self::MODELS;
    }

    /**
     * Record token usage for a specific tool.
     *
     * Accumulates prompt and completion token counts in the
     * 'adverto_token_usage' wp_option. Supported tool keys: alt-text, seo, llm.
     *
     * @param  string   $tool              Tool identifier (e.g. 'alt-text', 'seo', 'llm').
     * @param  int      $prompt_tokens     Number of prompt/input tokens used.
     * @param  int      $completion_tokens Number of completion/output tokens used.
     * @param  string   $model             Optional. Model ID used for this call.
     * @return void
     */
    public static function record_usage( $tool, $prompt_tokens, $completion_tokens, $model = null ) {
        $tool              = sanitize_key( $tool );
        $prompt_tokens     = absint( $prompt_tokens );
        $completion_tokens = absint( $completion_tokens );

        $usage = get_option( self::OPTION_KEY, array() );

        if ( ! isset( $usage[ $tool ] ) ) {
            $usage[ $tool ] = array(
                'prompt'     => 0,
                'completion' => 0,
            );
        }

        $usage[ $tool ]['prompt']     += $prompt_tokens;
        $usage[ $tool ]['completion'] += $completion_tokens;

        update_option( self::OPTION_KEY, $usage, false );
    }

    /**
     * Return a per-tool usage summary with estimated costs.
     *
     * Costs are calculated using the currently selected model's pricing.
     * The 'total_cost_usd' key holds the sum across all tools.
     *
     * @return array {
     *     Associative array keyed by tool name, plus 'total_cost_usd'.
     *
     *     @type array  $tool_name {
     *         @type int   $prompt_tokens
     *         @type int   $completion_tokens
     *         @type float $estimated_cost_usd
     *     }
     *     @type float $total_cost_usd
     * }
     */
    public static function get_summary() {
        $usage      = get_option( self::OPTION_KEY, array() );
        $model_id   = self::get_selected_model();
        $model_info = self::get_model_info( $model_id );

        // Pricing is per 1 million tokens.
        $input_rate  = $model_info['input']  / 1000000;
        $output_rate = $model_info['output'] / 1000000;

        $summary    = array();
        $total_cost = 0.0;

        foreach ( $usage as $tool => $tokens ) {
            $prompt     = isset( $tokens['prompt'] )     ? absint( $tokens['prompt'] )     : 0;
            $completion = isset( $tokens['completion'] ) ? absint( $tokens['completion'] ) : 0;

            $cost = round( ( $prompt * $input_rate ) + ( $completion * $output_rate ), 6 );

            $summary[ $tool ] = array(
                'prompt_tokens'      => $prompt,
                'completion_tokens'  => $completion,
                'estimated_cost_usd' => $cost,
            );

            $total_cost += $cost;
        }

        $summary['total_cost_usd'] = round( $total_cost, 6 );

        return $summary;
    }

    /**
     * Delete all stored token usage data.
     *
     * @return void
     */
    public static function reset_usage() {
        delete_option( self::OPTION_KEY );
    }
}
