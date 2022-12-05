<?php
require_once URLSLAB_PLUGIN_DIR . '/includes/cron/class-urlslab-cron.php';

class Urlslab_Update_Urls_Cron extends Urlslab_Cron {

	protected function execute(): bool {
		global $wpdb;
		$url_row = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . URLSLAB_URLS_TABLE . " WHERE status<>%s AND (urlTitle is null or urlTitle='' or urlMetaDescription is null or urlMetaDescription='') LIMIT 1", // phpcs:ignore
				Urlslab_Url_Row::STATUS_BROKEN
			),
			ARRAY_A
		);
		if ( empty( $url_row ) ) {
			return false;
		}

		$url = new Urlslab_Url_Row( $url_row );
		if ( empty( $url->get( 'urlTitle' ) ) ) {
			$url->set( 'urlTitle', Urlslab_Url_Row::VALUE_EMPTY );
		}
		if ( empty( $url->get( 'urlMetaDescription' ) ) ) {
			$url->set( 'urlMetaDescription', Urlslab_Url_Row::VALUE_EMPTY );
		}
		$url->set( 'updateStatusDate', Urlslab_Url_Row::get_now() );
		$url->update();    //lock the entry, so no other process will start working on it

		return $this->updateUrl( $url );
	}

	private function updateUrl( Urlslab_Url_Row $url ) {
		$page_content_file_name = download_url( $url->get_url()->get_url_with_protocol() );

		if ( empty( $page_content_file_name ) || is_wp_error( $page_content_file_name ) ) {
			$url->set( 'urlTitle', Urlslab_Url_Row::VALUE_EMPTY );
			$url->set( 'urlMetaDescription', Urlslab_Url_Row::VALUE_EMPTY );
			if ( isset( $page_content_file_name['errors']['http_404'] ) ) {
				$url->set( 'status', Urlslab_Url_Row::STATUS_BROKEN );
			}
		} else {
			$document                      = new DOMDocument( '1.0', get_bloginfo( 'charset' ) );
			$document->encoding            = 'utf-8';
			$document->strictErrorChecking = false; // phpcs:ignore
			$libxml_previous_state         = libxml_use_internal_errors( true );

			try {
				$document->loadHTML(
					mb_convert_encoding( file_get_contents( $page_content_file_name ), 'HTML-ENTITIES', 'utf-8' ),
					LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_BIGLINES | LIBXML_PARSEHUGE
				);
				libxml_clear_errors();
				libxml_use_internal_errors( $libxml_previous_state );


				// find the title
				if ( $url->get( 'urlTitle' ) == Urlslab_Url_Row::VALUE_EMPTY ) {
					$titlelist = $document->getElementsByTagName( 'title' );
					if ( $titlelist->length > 0 ) {
						$url->set( 'urlTitle', $titlelist->item( 0 )->nodeValue );
						if ( empty( $url->get( 'urlTitle' ) ) ) {
							$url->set( 'urlTitle', Urlslab_Url_Row::VALUE_EMPTY );
						}
					} else {
						$url->set( 'urlTitle', Urlslab_Url_Row::VALUE_EMPTY );
					}
				}

				if ( $url->get( 'urlMetaDescription' ) == Urlslab_Url_Row::VALUE_EMPTY ) {
					$xpath            = new DOMXPath( $document );
					$metadescriptions = $xpath->evaluate( '//meta[@name="description"]/@content' );
					if ( $metadescriptions->length > 0 ) {
						$url->set( 'urlMetaDescription', $xpath->evaluate( '//meta[@name="description"]/@content' )->item( 0 )->value );
						if ( empty( $url->get( 'urlMetaDescription' ) ) ) {
							$url->set( 'urlMetaDescription', Urlslab_Url_Row::VALUE_EMPTY );
						}
					} else {
						$url->set( 'urlMetaDescription', Urlslab_Url_Row::VALUE_EMPTY );
					}
				}
			} catch ( Exception $e ) {
			}
			unlink( $page_content_file_name );
		}

		$url->set( 'updateStatusDate', Urlslab_Url_Row::get_now() );

		return $url->update();
	}
}
