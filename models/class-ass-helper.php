<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'ASS_Helper' ) ) {

    /**
     * Class ASS_Helper
     *
     * Model that houses various helper functions utilized across After Sale Surveys plugin.
     *
     * @since 1.0.0
     */
    final class ASS_Helper {

        /**
         * Get all the pages of the current site via wpdb.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $limit
         * @param string $order_by
         * @return mixed
         */
        public static function get_all_site_pages( $limit = null , $order_by = 'DESC' ) {

            global $wpdb;

            $query = "
                      SELECT * FROM $wpdb->posts
                      WHERE $wpdb->posts.post_status = 'publish'
                      AND $wpdb->posts.post_type = 'page'
                      ORDER BY $wpdb->posts.post_date " . $order_by . "
                     ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get all the surveys of the current site via wpdb.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $limit
         * @param array $post_status
         * @param string $order_by
         * @return mixed
         */
        public static function get_all_surveys( $limit = null , $post_status = array( 'publish' ) , $order_by = 'DESC' ) {

            $constants = ASS_Constants::instance();
            global $wpdb;

            $comma_count     = count( $post_status ) - 1;
            $post_status_str = "";

            foreach ( $post_status as $stat ) {

                $post_status_str .= "'" . $stat . "'";

                if ( $comma_count > 0 ) {

                    $post_status_str .= ",";
                    $comma_count--;

                }

            }

            $query = "
                      SELECT * FROM $wpdb->posts
                      WHERE $wpdb->posts.post_status IN (" . $post_status_str . ")
                      AND $wpdb->posts.post_type = '" . $constants->SURVEY_CPT_NAME() . "'
                      ORDER BY post_date " . $order_by . "
                     ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get all surveys an order is participated with.
         *
         * @since 1.0.1
         * @access public
         *
         * @param $order_id int/string Order Id.
         * @param $limit null/int/string Limit of results to retrieve.
         * @param $post_status array Posts status of posts to retrieve.
         * @param $order_by string Order type, descending or ascending.
         * @return array Array of stdClass object that represents surveys.
         */
        public static function get_all_surveys_an_order_participated( $order_id , $limit = null , $post_status = array( 'publish' ) , $order_by = 'DESC' ) {

            $constants = ASS_Constants::instance();
            global $wpdb;

            $comma_count     = count( $post_status ) - 1;
            $post_status_str = "";

            foreach ( $post_status as $stat ) {

                $post_status_str .= "'" . $stat . "'";

                if ( $comma_count > 0 ) {

                    $post_status_str .= ",";
                    $comma_count--;

                }

            }

            $query = "
                        SELECT * FROM $wpdb->posts
                        WHERE $wpdb->posts.post_status IN (" . $post_status_str . ") AND $wpdb->posts.post_type = '" . $constants->SURVEY_CPT_NAME() . "'
                        AND $wpdb->posts.ID IN (
                            SELECT meta_value FROM $wpdb->postmeta
                            INNER JOIN $wpdb->posts
                            ON $wpdb->posts.ID = $wpdb->postmeta.post_id
                            WHERE $wpdb->postmeta.meta_key = '" . $constants->POST_META_RESPONSE_SURVEY_ID() . "' &&
                            $wpdb->posts.ID IN (
                                SELECT ID FROM $wpdb->posts post_table
                                INNER JOIN $wpdb->postmeta post_meta_table
                                ON post_table.ID = post_meta_table.post_id
                                WHERE post_table.post_status IN ( 'publish' ) AND post_table.post_type = '" . $constants->SURVEY_RESPONSE_CPT_NAME() . "'
                                AND post_meta_table.meta_key = '" . $constants->POST_META_RESPONSE_ORDER_ID() . "' AND post_meta_table.meta_value = '" . $order_id . "'
                                ORDER BY post_table.post_date " . $order_by . "
                            )
                        )
                    ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get all surveys an order has not participated with.
         *
         * @since 1.0.1
         * @access public
         *
         * @param $order_id int/string Order Id.
         * @param $limit null/int/string Limit of results to retrieve.
         * @param $post_status array Posts status of posts to retrieve.
         * @param $order_by string Order type, descending or ascending.
         * @return array Array of stdClass object that represents surveys.
         */
        public static function get_all_surveys_an_order_has_not_participated( $order_id , $limit = null , $post_status = array( 'publish' ) , $order_by = 'DESC' ) {

            $constants = ASS_Constants::instance();
            global $wpdb;

            $comma_count     = count( $post_status ) - 1;
            $post_status_str = "";

            foreach ( $post_status as $stat ) {

                $post_status_str .= "'" . $stat . "'";

                if ( $comma_count > 0 ) {

                    $post_status_str .= ",";
                    $comma_count--;

                }

            }

            $query = "
                        SELECT * FROM $wpdb->posts
                        WHERE $wpdb->posts.post_status IN (" . $post_status_str . ") AND $wpdb->posts.post_type = '" . $constants->SURVEY_CPT_NAME() . "'
                        AND $wpdb->posts.ID IN (
                            SELECT meta_value FROM $wpdb->postmeta
                            INNER JOIN $wpdb->posts
                            ON $wpdb->posts.ID = $wpdb->postmeta.post_id
                            WHERE $wpdb->postmeta.meta_key = '" . $constants->POST_META_RESPONSE_SURVEY_ID() . "' &&
                            $wpdb->posts.ID IN (
                                SELECT ID FROM $wpdb->posts post_table
                                INNER JOIN $wpdb->postmeta post_meta_table
                                ON post_table.ID = post_meta_table.post_id
                                WHERE post_table.post_status IN ( 'publish' ) AND post_table.post_type = '" . $constants->SURVEY_RESPONSE_CPT_NAME() . "'
                                AND post_meta_table.meta_key = '" . $constants->POST_META_RESPONSE_ORDER_ID() . "' AND post_meta_table.meta_value != '" . $order_id . "'
                                ORDER BY post_table.post_date " . $order_by . "
                            )
                        )
                    ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get all surveys a customer has participated.
         *
         * @since 1.0.1
         * @access public
         *
         * @param $email string Customer email.
         * @param $limit null/int/string Limit of results to retrieve.
         * @param $post_status array Posts status of posts to retrieve.
         * @param $order_by string Order type, descending or ascending.
         * @return array Array of stdClass object that represents surveys.
         */
        public static function get_all_surveys_a_customer_participated( $email , $limit = null , $post_status = array( 'publish' ) , $order_by = 'DESC' ) {

            $constants = ASS_Constants::instance();
            global $wpdb;

            $comma_count     = count( $post_status ) - 1;
            $post_status_str = "";

            foreach ( $post_status as $stat ) {

                $post_status_str .= "'" . $stat . "'";

                if ( $comma_count > 0 ) {

                    $post_status_str .= ",";
                    $comma_count--;

                }

            }

            $query = "
                        SELECT * FROM $wpdb->posts
                        WHERE $wpdb->posts.post_status IN (" . $post_status_str . ") AND $wpdb->posts.post_type = '" . $constants->SURVEY_CPT_NAME() . "'
                        AND $wpdb->posts.ID IN (
                            SELECT meta_value FROM $wpdb->postmeta
                            INNER JOIN $wpdb->posts
                            ON $wpdb->posts.ID = $wpdb->postmeta.post_id
                            WHERE $wpdb->postmeta.meta_key = '" . $constants->POST_META_RESPONSE_SURVEY_ID() . "' &&
                            $wpdb->posts.ID IN (
                                SELECT ID FROM $wpdb->posts post_table
                                INNER JOIN $wpdb->postmeta post_meta_table
                                ON post_table.ID = post_meta_table.post_id
                                WHERE post_table.post_status IN ( 'publish' ) AND post_table.post_type = '" . $constants->SURVEY_RESPONSE_CPT_NAME() . "'
                                AND post_meta_table.meta_key = '" . $constants->POST_META_RESPONSE_USER_EMAIL() . "' AND post_meta_table.meta_value = '" . $email . "'
                                ORDER BY post_table.post_date " . $order_by . "
                            )
                        )
                    ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get all surveys a customer has not participated.
         *
         * @since 1.0.1
         * @access public
         *
         * @param $email string Customer email.
         * @param $limit null/int/string Limit of results to retrieve.
         * @param $post_status array Posts status of posts to retrieve.
         * @param $order_by string Order type, descending or ascending.
         * @return array Array of stdClass object that represents surveys.
         */
        public static function get_all_surveys_a_customer_has_not_participated( $email , $limit = null , $post_status = array( 'publish' ) , $order_by = 'DESC' ) {

            $constants = ASS_Constants::instance();
            global $wpdb;

            $comma_count     = count( $post_status ) - 1;
            $post_status_str = "";

            foreach ( $post_status as $stat ) {

                $post_status_str .= "'" . $stat . "'";

                if ( $comma_count > 0 ) {

                    $post_status_str .= ",";
                    $comma_count--;

                }

            }

            $query = "
                        SELECT * FROM $wpdb->posts
                        WHERE $wpdb->posts.post_status IN (" . $post_status_str . ") AND $wpdb->posts.post_type = '" . $constants->SURVEY_CPT_NAME() . "'
                        AND $wpdb->posts.ID IN (
                            SELECT meta_value FROM $wpdb->postmeta
                            INNER JOIN $wpdb->posts
                            ON $wpdb->posts.ID = $wpdb->postmeta.post_id
                            WHERE $wpdb->postmeta.meta_key = '" . $constants->POST_META_RESPONSE_SURVEY_ID() . "' &&
                            $wpdb->posts.ID IN (
                                SELECT ID FROM $wpdb->posts post_table
                                INNER JOIN $wpdb->postmeta post_meta_table
                                ON post_table.ID = post_meta_table.post_id
                                WHERE post_table.post_status IN ( 'publish' ) AND post_table.post_type = '" . $constants->SURVEY_RESPONSE_CPT_NAME() . "'
                                AND post_meta_table.meta_key = '" . $constants->POST_META_RESPONSE_USER_EMAIL() . "' AND post_meta_table.meta_value != '" . $email . "'
                                ORDER BY post_table.post_date " . $order_by . "
                            )
                        )
                    ";


            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get all the responses of a survey.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $survey_id
         * @param null $filters
         * @return mixed
         */
        public static function get_survey_responses( $survey_id , $filters = null ) {

            $constants = ASS_Constants::instance();
            global $wpdb;

            $query = "
                      SELECT * FROM $wpdb->posts post_table
                      INNER JOIN $wpdb->postmeta post_meta_table
                      ON post_meta_table.post_id = post_table.ID
                      WHERE post_meta_table.meta_key = '" . $constants->POST_META_RESPONSE_SURVEY_ID() . "'
                      AND post_meta_table.meta_value = " . $survey_id;

            if ( !is_null( $filters ) ) {

                $filter_count      = count( $filters );
                $filtered_post_ids = array();
                $error_filters     = array( 'error_filters' => array() );

                foreach ( $filters as $filter ) {

                    if ( $filter[ 'filter_type' ] == 'date_range' ) {

                        $from_date = trim( $filter[ 'from_date' ] );
                        $to_date   = trim( $filter[ 'to_date' ] );

                        if ( $from_date ) {

                            if ( self::validate_date( $from_date ) ) {

                                $from_date = strtotime( $from_date );
                                $from_date = date( 'Y-m-d' , $from_date ) . " 00:00:00"; // 12:00 am

                            } else
                                $error_filters[ 'error_filters' ][] = __( 'Invalid <b>From Date</b> field value' , 'after-sale-surveys' );

                        }

                        if ( $to_date ) {

                            if ( self::validate_date( $to_date ) ) {

                                $to_date   = strtotime( $to_date );
                                $to_date   = date( 'Y-m-d' , $to_date ) . " 23:59:59"; // 11:59:59 pm

                            } else
                                $error_filters[ 'error_filters' ][] = __( 'Invalid <b>To Date</b> field value' , 'after-sale-surveys' );

                        }

                        if ( $from_date && $to_date )
                            $query .= " AND post_table.post_date >= '" . $from_date . "' AND post_table.post_date <= '" . $to_date . "'";
                        elseif ( $from_date && !$to_date )
                            $query .= " AND post_table.post_date >= '" . $from_date  . "'";
                        elseif ( !$from_date && $to_date )
                            $query .= " AND post_table.post_date <= '" . $to_date . "'";

                    } else {

                        $filtered_posts = apply_filters( 'as_survey_responses_query_' . $filter[ 'filter_type' ] . '_filter' , array() , $survey_id , $filter );

                        if ( !is_array( $filtered_posts ) )
                            $filtered_posts = array();

                        if ( array_key_exists( 'error_filters' , $filtered_posts ) )
                            $error_filters[ 'error_filters' ] = array_merge( $error_filters[ 'error_filters' ] , $filtered_posts[ 'error_filters' ] );
                        else {

                            if ( $filter_count == 1 ) {

                                // If there is only 1 filter, and its not the filters supported by ASS, then we just return
                                // the resulting filtered posts.
                                return $filtered_posts;

                            } else {

                                $post_ids = array();
                                foreach ( $filtered_posts as $filtered_post )
                                    $post_ids[] = $filtered_post->ID;

                                $filtered_post_ids = array_merge( $filtered_post_ids , $post_ids );

                            }

                        }

                    }

                }

                if ( !empty( $error_filters[ 'error_filters' ] ) )
                    return $error_filters;

                if ( !empty( $filtered_post_ids ) ) {

                    $filtered_post_ids = array_unique( $filtered_post_ids );
                    $post_ids_str = implode( ',' , $filtered_post_ids );
                    $query .= ' AND post_table.ID IN (' . $post_ids_str . ')';

                }

            }

            $query .= " ORDER BY post_table.post_date DESC";

            $query = apply_filters( "as_survey_responses_query" , $query , $filters );

            return $wpdb->get_results( $query );

        }

        /**
         * Validate date.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $date
         * @param string $format
         * @return bool
         */
        public static function validate_date( $date , $format = 'F j, Y' ) {

            $d = DateTime::createFromFormat( $format , $date );
            return $d && $d->format( $format ) == $date;

        }

        /**
         * Get client ip.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public static function get_client_ip() {

            if ( !empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {

                $ips = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
                $ips = explode( ',' , $ips );
                $ips = array_map( 'trim' , $ips );
                $ip  = array_pop( $ips );

            } else
                $ip = $_SERVER['REMOTE_ADDR'];

            return $ip;

        }

	    /**
		 * Returns the timezone string for a site, even if it's set to a UTC offset
		 *
		 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
		 *
		 * Reference:
		 * http://www.skyverge.com/blog/down-the-rabbit-hole-wordpress-and-timezones/
		 *
		 * @since 1.1.0
		 * @access public
		 *
		 * @return string valid PHP timezone string
		 */
		public static function get_site_current_timezone() {

			// if site timezone string exists, return it
			if ( $timezone = get_option( 'timezone_string' ) )
				return $timezone;

			// get UTC offset, if it isn't set then return UTC
			if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) )
				return 'UTC';

			return self::convert_utc_offset_to_timezone( $utc_offset );

		}

		/**
		 * Conver UTC offset to timezone.
		 *
		 * @since 1.1.0
		 * @access public
		 *
		 * @param $utc_offset float/int/sting UTC offset.
		 * @return string valid PHP timezone string
		 */
		public static function convert_utc_offset_to_timezone( $utc_offset ) {

			// adjust UTC offset from hours to seconds
			$utc_offset *= 3600;

			// attempt to guess the timezone string from the UTC offset
			if ( $timezone = timezone_name_from_abbr( '' , $utc_offset , 0 ) )
				return $timezone;

			// last try, guess timezone string manually
			$is_dst = date( 'I' );

			foreach ( timezone_abbreviations_list() as $abbr ) {

				foreach ( $abbr as $city ) {

					if ( $city[ 'dst' ] == $is_dst && $city[ 'offset' ] == $utc_offset )
						return $city[ 'timezone_id' ];

				}

			}

			// fallback to UTC
			return 'UTC';

		}

		/**
		 * Check if current user is authorized to execute an operation within the 'After Sale Surveys' plugin.
		 *
		 * @access public
		 * @since 1.1.0
		 *
		 * @param null $user
		 * @return bool
		 */
		public static function current_user_authorized( $user = null ) {

			$constants = ASS_Constants::instance();

			$ass_admin_roles = $constants->ROLES_ALLOWED_TO_MANAGE_ASS();

			if ( is_null( $user ) )
				$user = wp_get_current_user();

			if ( $user->ID ) {

				if ( count( array_intersect( ( array ) $user->roles , $ass_admin_roles ) ) )
					return true;
				else
					return false;

			} else
				return false;

		}

        /**
         * It returns an array of Post objects.
         * Get all products of the shop via $wpdb.
         *
         * @since 1.1.0
         * @return mixed
         *
         * @param null $limit
         * @param string $order_by
         * @return mixed
         */
        public static function get_all_products( $limit = null , $order_by = 'DESC' ) {

            global $wpdb;

            $order_by = filter_var( $order_by , FILTER_SANITIZE_STRING );

            $query = "
                      SELECT *
                      FROM $wpdb->posts
                      WHERE post_status = 'publish'
                      AND post_type = 'product'
                      ORDER BY $wpdb->posts.post_date " . $order_by . "
                    ";

            if ( $limit && is_numeric( $limit ) )
                $query .= " LIMIT " . $limit;

            return $wpdb->get_results( $query );

        }

        /**
         * Get variable product variations.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $args
         * @return array
         */
        public static function get_product_variations( $args ) {

            if ( isset( $args[ 'product' ] ) )
                $product = $args[ 'product' ];
            elseif ( isset( $args[ 'variable_id' ] ) )
                $product = wc_get_product( $args[ 'variable_id' ] );

			$variation_arr = array();

			if ( $product ) {

				$product_variations = $product->get_available_variations();
				$product_attributes = $product->get_attributes();

				foreach ( $product_variations as $variation ) {

					if ( isset( $args[ 'variation_id' ] ) && $args[ 'variation_id' ] != $variation[ 'variation_id' ] )
						continue;

					$variation_obj            = wc_get_product( $variation[ 'variation_id' ] );
					$variation_attributes     = $variation_obj->get_variation_attributes();
					$friendly_variation_text  = null;
					$variation_attributes_arr = array();

					foreach ( $variation_attributes as $variation_name => $variation_val ) {

						foreach ( $product_attributes as $attribute_key => $attribute_arr ) {

							if ( $variation_name != 'attribute_' . sanitize_title( $attribute_arr[ 'name' ] ) )
								continue;

							$attr_found = false;

							if ( $attribute_arr[ 'is_taxonomy' ] ) {

								// This is a taxonomy attribute
								$variation_taxonomy_attribute = wp_get_post_terms( $product->id , $attribute_arr[ 'name' ] );

								foreach ( $variation_taxonomy_attribute as $var_tax_attr ) {

									if ( $variation_val == $var_tax_attr->slug ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": " . $var_tax_attr->name;
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": " . $var_tax_attr->name;

										$attr_key = "attribute_pa_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) ) );
										$attr_val = $var_tax_attr->slug;

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = $attr_val;
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => $attr_val );

										$attr_found = true;
										break;

									} elseif ( empty( $variation_val ) ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";

										$attr_key = "attribute_pa_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) ) );

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = "any";
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => "any" );

										$attr_found = true;
										break;

									}

								}

							} else {

								// This is not a taxonomy attribute

								$attr_val = explode( '|' , $attribute_arr[ 'value' ] );

								foreach ( $attr_val as $attr ) {

									$attr = trim( $attr );

									// I believe the reason why I wrapped the $attr with sanitize_title is to remove special chars
									// We need ot wrap variation_val too to properly compare them
									if ( sanitize_title( $variation_val ) == sanitize_title( $attr ) ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , $attribute_arr[ 'name' ] ) . ": " . $attr;
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , $attribute_arr[ 'name' ] ) . ": " . $attr;

										$attr_key = "attribute_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , $attribute_arr[ 'name' ] ) ) );

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = $attr;
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => $attr );

										$attr_found = true;
										break;

									} elseif ( empty( $variation_val ) ) {

										if ( is_null( $friendly_variation_text ) )
											$friendly_variation_text = str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";
										else
											$friendly_variation_text .= ", " . str_replace( ":" , "" , wc_attribute_label( $attribute_arr[ 'name' ] ) ) . ": Any";

										$attr_key = "attribute_" . str_replace( " " , "-" , strtolower( str_replace( ":" , "" , $attribute_arr[ 'name' ] ) ) );

										if ( isset( $variation_attributes_arr[ $variation[ 'variation_id' ] ] ) )
											$variation_attributes_arr[ $variation[ 'variation_id' ] ][ $attr_key ] = "Any";
										else
											$variation_attributes_arr[ $variation[ 'variation_id' ] ] = array( $attr_key => "Any" );

										$attr_found = true;
										break;

									}

								}

							}

							if ( $attr_found )
								break;

						}

					}

					if ( ( $product->managing_stock() === true && $product->get_total_stock() > 0 && $variation_obj->managing_stock() === true && $variation_obj->get_total_stock() > 0 && $variation_obj->is_purchasable() ) ||
						 ( $product->managing_stock() !== true && $variation_obj->is_in_stock() && $variation_obj->is_purchasable() ) ||
						 ( $variation_obj->backorders_allowed() && $variation_obj->is_purchasable() ) ) {

						$variation_arr[] = array(
							'value'      => $variation[ 'variation_id' ],
							'text'       => $friendly_variation_text,
							'disabled'   => false,
							'visible'    => true,
							'attributes' => $variation_attributes_arr
						);

					} else {

						$visibility = false;
						if ( $variation_obj->variation_is_visible() )
							$visibility = true;

						$variation_arr[] = array(
							'value'      => 0,
							'text'       => $friendly_variation_text,
							'disabled'   => true,
							'visible'    => $visibility,
							'attributes' => $variation_attributes_arr
						);

					}

				}

				wp_reset_postdata();

				usort( $variation_arr , array( 'ASS_Helper' , 'usort_variation_menu_order') ); // Sort variations via menu order

			}

            return $variation_arr;

        }

        /**
         * usort callback that sorts variations based on menu order.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $arr1
         * @param $arr2
         * @return int
         */
        public static function usort_variation_menu_order( $arr1 , $arr2 ) {

            $product1_id = $arr1[ 'value' ];
            $product2_id = $arr2[ 'value' ];

            $product1_menu_order = get_post_field( 'menu_order', $product1_id );
            $product2_menu_order = get_post_field( 'menu_order', $product2_id );

            if ( $product1_menu_order == $product2_menu_order )
                return 0;

            return ( $product1_menu_order < $product2_menu_order ) ? -1 : 1;

        }

        /**
		 * Get all the product category terms of the current site via wpdb.
		 *
		 * @since 1.1.0
		 * @access public
		 *
		 * @param null $limit
		 * @param string $order_by
		 * @return mixed
		 */
		public static function get_all_product_category_terms( $limit = null , $order_by = 'DESC' ) {

			global $wpdb;

            $order_by = filter_var( $order_by , FILTER_SANITIZE_STRING );

			$query = "
					  SELECT * FROM $wpdb->terms
					  INNER JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
					  WHERE $wpdb->term_taxonomy.taxonomy = 'product_cat'
					  ORDER BY $wpdb->terms.name " . $order_by . "
				     ";

			if ( $limit && is_numeric( $limit ) )
				$query .= " LIMIT " . $limit;

			return $wpdb->get_results( $query );

		}

        /**
		 * Get the total respondents of a survey
		 *
		 * @since 1.1.1
		 * @access public
		 *
		 * @param int $survey_id  ID of as_survey CPT post
		 * @return int
		 */
        public static function get_survey_total_respondents( $survey_id ) {

            $args = array(
                'post_type'  => AS_Surveys()->constants->SURVEY_RESPONSE_CPT_NAME(),
                'meta_key'   => AS_Surveys()->constants->POST_META_RESPONSE_SURVEY_ID(),
                'meta_value' => $survey_id
            );

            $query = new WP_Query( $args );

            return $query->post_count;

        }

        /**
		 * Get the respondent data of a survey response.
		 *
		 * @since 1.1.1
		 * @access public
         *
		 * @param int $response_id ID of a as_survey_response CPT post
		 * @return array  Array of respondent data (IP, User Agent, Response Date)
		 */
        public static function get_survey_response_respondent_data( $response_id = 0 ) {

            global $wpdb;

            // make sure response_id is int
            $response_id = (int) $response_id;

            $client_info = $wpdb->get_row( "SELECT client_ip , user_agent , record_datetime FROM " . $wpdb->prefix . "ass_survey_completions WHERE response_id = $response_id" );

            if ( ! is_object( $client_info ) || empty( $client_info ) )
                return array();

        	return array(
                    'ip_address'    =>  $client_info->client_ip,
                    'browser'       =>  $client_info->user_agent,
                    'response_date' =>  $client_info->record_datetime,

            );
        }

        /**
         * Get data about the current woocommerce installation.
         *
         * @since 1.1.2
         * @access public
         * @return array Array of data about the current woocommerce installation.
         */
        public static function get_woocommerce_data() {

            if ( ! function_exists( 'get_plugin_data' ) )
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

            return get_plugin_data( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' );

        }

        /**
         * Get order properties based on the key. WC 2.7
         *
         * @since 1.1.2
         * @access public
         *
         * @param WC_Order $order  order object
         * @param string   $key    order property
         * @return string   order property
         */
        public static function get_order_data( $order , $key ) {

            if ( is_a( $order , 'WC_Order' ) ) {

                $woocommerce_data = self::get_woocommerce_data();

                if ( version_compare( $woocommerce_data[ 'Version' ] , '2.7.0' , '>=' ) || $woocommerce_data[ 'Version' ] === '2.7.0-RC1' ) {

                    switch ( $key ) {

                        case 'order_total' :
                            return $order->get_total();
                            break;

                        default:
                            $key = 'get_' . $key;
                            return $order->$key();
                            break;
                    }

                } else
                    return $order->$key;

            } else {

                error_log( 'ASS Error : get_order_data helper functions expect parameter $order of type WC_Order.' );
                return 0;

            }

        }

    }

}
