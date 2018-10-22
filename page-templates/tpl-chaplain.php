<?php
/**
 * Template Name: Chaplain Template
 *
 * @link https://developer.wordpress.org/themes/template-files-section/page-template-files/
 *
 * @package awps
 */

get_header(); ?>

<div class="container">

	<div class="row">

		<div class="col-sm-12">

			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">

					<?php
					/* Start the Loop */
					while ( have_posts() ) :
						the_post();

						// Include the page content template.
						get_template_part( 'views/content', 'page' );

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
						
						// End of the loop.
					endwhile;
					
					/* Podio */
					// Load dependencies
					require __DIR__ . '/../vendor/autoload.php';

					// Loading environment variables
					$dotenv = new Dotenv\Dotenv(__DIR__);
					$dotenv->load();

					// Getting variables from .env for Authentication
					define( 'CLIENT_ID', getenv( 'CLIENT_ID' ) );
					$client_id = CLIENT_ID;
					define( 'CLIENT_SECRET', getenv( 'CLIENT_SECRET' ) );
					$client_secret = CLIENT_SECRET;

					// CRM | APP Schools
					define( 'SCHOOLS_APP_ID', getenv( 'SCHOOLS_APP_ID' ) );
					$app_id_schools = SCHOOLS_APP_ID;
					define( 'SCHOOLS_APP_TOKEN', getenv( 'SCHOOLS_APP_TOKEN' ) );
					$app_tokens_schools = SCHOOLS_APP_TOKEN;

					// CRM | APP Schools
					define( 'CHAP_APP_ID', getenv( 'CHAP_APP_ID' ) );
					$app_id_chap_app = CHAP_APP_ID;
					define( 'CHAP_APP_TOKEN', getenv( 'CHAP_APP_TOKEN' ) );
					$app_token_chap_app = CHAP_APP_TOKEN;

					// Chaplaincy-admin | Chap Position 

					define( 'CHAP_POS_ID', getenv( 'CHAP_POS_ID' ) );
					$app_id_chap_pos = CHAP_POS_ID;
					define( 'CHAP_POS_TOKEN', getenv( 'CHAP_POS_TOKEN' ) );
					$app_token_chap_pos = CHAP_POS_TOKEN;

					
					// Schools 

					// Authentication with App
					Podio::setup($client_id, $client_secret);
					Podio::authenticate_with_app($app_id_chap_app, $app_token_chap_app);
					// You can now make API calls.

					// View | Active, Paused, Vacant by RCM | Chap App
					$view_id = 28163687;

					// Aberdeen Public School
					// $item_id = 384971115;
					// $app_item_id =243;

					/* ------------------------------------------------------------------------- */
					/* Number of items in a view */

					// $count = PodioItem::get_count($app_id);
					$items_view = Podio::get("/item/app/{$app_id_chap_app}/count?view_id={$view_id}")->json_body();
					// var_dump($count);
					$count = $items_view['count'];
					// print $count;

					/* ------------------------------------------------------------------------- */

					/* Get items from view */
					$maxItems = 8;
					// $filters = array('field_id' => 115371917 );
					$offset = 1;
					// $collection = PodioItem::filter_by_view($app_id_chap_app, $view_id, array('limit' => $maxItems, 'offset' => $offset, 'filters' => $filters ), array('fields' => 'items.view(micro)'));
					$collection = PodioItem::filter_by_view($app_id_chap_app, $view_id, array('limit' => $maxItems, 'offset' => $offset), array('fields' => 'items.view(full)'));

					// print_r($items);
					//print $items;
					// var_dump($collection);
					
					// Address Field (Location)
					//$field_id = 103463282;

					$i = 1;

					foreach ($collection as $item) {
						// print "item_id: " . $item->item_id . "</br>";
						// print "app_item_id: " . $item->app_item_id . "</br>";
						print $i . ". School: " . $item->title . "</br>";
						// print "link: " . $item->link . "</br>";
						// print "app_item_id_formatted: " . $item->app_item_id_formatted . "</br></br>";
						// var_dump($item);
						// print json_encode($item);
						
						// Podio::setup($client_id, $client_secret);
						// Podio::authenticate_with_app($app_id_schools, $app_tokens_schools);
						// $response = Podio::get("/item/{$item->item_id}/value/{$field_id}/v2");
						// $obj = json_decode($response->body);
						// $values = json_encode($obj->values);
						// $item = json_decode($values);
						// print "Address: " . $item[0]->value;
						// print "Address: " . school_get_address($item->item_id);

						// 115371917 = Schools Field Id
						foreach ($item->fields as $field) {
							if ($field->field_id == 115371917) {
								foreach ($field->values as $value) {
									print "--Address: " . school_get_address($value->item_id) . "</br>";
								}
							}
							
							// 168482883 = Chaplain Positions Field ID
							if ($field->field_id == 168482883) {
								print "--Chaplain Positions:</br>";
								$j=1;
								foreach ($field->values as $value) {
									print "----" . $j . ". " . $value->title . "</br>";
									// var_dump($value->item_id);
									print "------ Hours: " .  chap_pos_get_hours($value->item_id) . "</br>";
									$j++;
									
								}
							}

							// 115371916 = Chaplain Field Id
							if ($field->field_id == 115371916) {
								foreach ($field->values as $value) {
									// var_dump($value->title);
									if ( $value->title == ''){
										print "--No Chaplain.";
									}
									else {
										print "--Chaplain: " . $value->title ; 	
									}
								}
							}

						}
						print "</br></br>";
						$i++;
					}


					function school_get_address($item_id) {
						// Authentication with App
						$client_id = CLIENT_ID;
						$client_secret = CLIENT_SECRET;
						$app_id_schools = SCHOOLS_APP_ID;
						$app_tokens_schools = SCHOOLS_APP_TOKEN;

						Podio::setup($client_id, $client_secret);
						Podio::authenticate_with_app($app_id_schools, $app_tokens_schools);

						// You can now make API calls.
						// Location field
						$field_id = 103463282;

						// https://developers.podio.com/doc/items/get-item-field-values-v2-144279511
						$response = Podio::get("/item/{$item_id}/value/{$field_id}/v2");

						// var_dump($response);

						// print $response->body;
						// print json_encode($response->body)->value;

						$obj = json_decode($response->body);
						// var_dump($obj);
						$values = json_encode($obj->values);

						$item = json_decode($values);
						// var_dump($item);
						return $item[0]->value;
						// var_dump($item);
					}

					function chap_pos_get_hours($item_id) {
						$client_id = CLIENT_ID;
						$client_secret = CLIENT_SECRET;
						$app_id = CHAP_POS_ID;
						$app_token = CHAP_POS_TOKEN;

						// Authentication with App
						Podio::setup($client_id, $client_secret);
						Podio::authenticate_with_app($app_id, $app_token);
						// You can now make API calls.


						// item from podio
						// $item_id = 813092658;
						// $item_id = int($item_id);
						// Current Active Hours	| current-active-hours
						$field_id = 172951075;


						// https://developers.podio.com/doc/items/get-item-field-values-v2-144279511
						$response = Podio::get("/item/{$item_id}/value/{$field_id}/v2");

						// var_dump($response->body);

						// print $response->body;
						// print json_encode($response->body)->value;

						$obj = json_decode($response->body);
						// var_dump($obj);
						return $obj->values;

					}

					?>

				</main><!-- #main -->
			</div><!-- #primary -->

		</div><!-- .col- -->

		<!-- <div class="col-sm-4"> -->
			<!-- <?php // get_sidebar(); ?> -->
		<!-- </div> --><!-- .col- -->

	</div><!-- .row -->

</div><!-- .container -->

<?php
get_footer();
