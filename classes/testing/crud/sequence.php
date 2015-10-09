<?php
/**
 * Sequence CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */


namespace ingot\testing\crud;


use ingot\testing\utility\helpers;

class sequence extends table_crud {

	/**
	 * Name of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'sequence';

	protected static function what() {
		return 'sequence';
	}

	/**
	 * Get a collection of items
	 *
	 * @since 0.0.5
	 *
	 * @param array $params {
	 *  $group_id int ID of group to get all
	 *  $ids array Optional. Array of ids to get.
	 *  $current bool Optional. Used with $ids or $group_id, if true, will return the first non-completed sequence for that group_id or set of ids. Default is false.
	 *  $limit int Optional. Limit results, default is -1 which gets all.
	 *  $page int Optional. Page of results, used with $limit. Default is 1
	 * }
	 *
	 * @return array
	 */
	public static function get_items( $params ) {
		$limit = $page = 1;
		$args = wp_parse_args(
			$params,
			array(
				'group_ID' => null,
				'ids' => array(),
				'current' => false,
				'limit' => -1,
				'page' => 1,
			)
		);

		if( -1 == $args[ 'limit' ] ) {
			$args[ 'limit' ] = 999999999;
		}

		global $wpdb;
		$table_name = self::get_table_name();
		if( helpers::v( 'group_ID', $args, null ) ){
			$sql = sprintf( 'SELECT * FROM `%s` WHERE `group_ID` = %d', $table_name, helpers::v( 'group_ID', $params )  );
		}elseif( ! empty( helpers::v( 'ids', $args, array() ) ) ){
			$in = implode( ',', helpers::v( 'ids', $params, array() ) );
			$sql = sprintf( 'SELECT * FROM `%s` WHERE `ID` IN( %s)',$table_name, $in );
		}else{
			$sql = sprintf( 'SELECT * FROM `%s`', $table_name );
		}

		if( helpers::v( 'current', $args, false ) ) {
			$sql .= ' AND `completed` != 1';
		}

		$sql .= sprintf( ' ORDER BY `ID` ASC LIMIT %d OFFSET %d', $args[ 'limit' ], self::calculate_offset( $args[ 'limit' ], $args[ 'page' ] )  );

		$results = $wpdb->get_results( $sql, ARRAY_A );

		return $results;

	}

	/**
	 * Mark a sequence as completed
	 *
	 * @since 0.0.7
	 *
	 * @param int $id Sequence ID.
	 *
	 * @return bool True if updated. False if not
	 */
	public static function complete( $id ) {
		global $wpdb;
		$table_name = static::get_table_name();
		$wpdb->update(
			$table_name,
			array( 'completed' => 1 ),
			array( 'ID' => $id )

		);

		if( $id == $wpdb->insert_id ) {
			return true;
		}else{
			return false;
		}



	}



	/**
	 * Required fields of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function required() {
		$required = array(
			'a_id',
			'b_id',
			'test_type',
		);

		return $required;
	}

	/**
	 * Neccasary, but not required fields of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {
		$needed = array(
			'a_win',
			'b_win',
			'a_total',
			'b_total',
			'initial',
			'completed',
			'threshold',
			'created',
			'modified',
			'group_ID',
		);

		return $needed;
	}



}
