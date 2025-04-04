<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://nexir.es/
 * @since      1.0.0
 *
 * @package    CCPO
 * @subpackage CCPO/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * This provides a central place to manage hooks and helps keep
 * the main plugin file and other classes cleaner.
 *
 * @package    CCPO
 * @subpackage CCPO/includes
 * @author     Nexir Marketing <info@nexir.es>
 */
class CCPO_Loader {

	/**
	 * The array of actions registered with WordPress.
	 * Each item is an array containing hook details.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 * Each item is an array containing hook details.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action hook.
	 * @param    object               $component        A reference to the instance of the object class where the callback is defined.
	 * @param    string               $callback         The name of the method (function) within the $component object.
	 * @param    int                  $priority         Optional. The execution priority. Default 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments the callback accepts. Default 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter hook.
	 * @param    object               $component        A reference to the instance of the object class where the callback is defined.
	 * @param    string               $callback         The name of the method (function) within the $component object.
	 * @param    int                  $priority         Optional. The execution priority. Default 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments the callback accepts. Default 1.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that aggregates the hooks (actions and filters).
	 * It prepares an array structure for each hook.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks (either $this->actions or $this->filters).
	 * @param    string               $hook             The name of the hook (action or filter tag).
	 * @param    object               $component        The object instance containing the callback.
	 * @param    string               $callback         The method name of the callback.
	 * @param    int                  $priority         Hook priority.
	 * @param    int                  $accepted_args    Number of accepted arguments for the callback.
	 * @return   array                                  The updated collection of hooks.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);
		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress by iterating
	 * through the collections and calling add_filter() and add_action().
	 *
	 * This should be called usually from the main plugin class's run() method.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook_details ) {
			add_filter(
				$hook_details['hook'],
				array( $hook_details['component'], $hook_details['callback'] ),
				$hook_details['priority'],
				$hook_details['accepted_args']
			);
		}

		foreach ( $this->actions as $hook_details ) {
			add_action(
				$hook_details['hook'],
				array( $hook_details['component'], $hook_details['callback'] ),
				$hook_details['priority'],
				$hook_details['accepted_args']
			);
		}
	}
} // End class 