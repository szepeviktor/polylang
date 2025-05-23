<?php

class Switcher_Test extends PLL_UnitTestCase {

	private $structure = '/%postname%/';
	protected $switcher;

	/**
	 * @param WP_UnitTest_Factory $factory
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		parent::wpSetUpBeforeClass( $factory );

		self::create_language( 'en_US' );
		self::create_language( 'fr_FR' );
		self::create_language( 'de_DE_formal' );

		self::require_api();
	}

	public function set_up() {
		parent::set_up();

		$links_model = self::$model->get_links_model();
		$this->frontend = new PLL_Frontend( $links_model );
		$this->frontend->init();

		// De-activate cache for links
		$this->frontend->links->cache = $this->getMockBuilder( 'PLL_Cache' )->getMock();
		$this->frontend->links->cache->method( 'get' )->willReturn( false );

		$this->switcher = new PLL_Switcher();
	}

	public function test_the_languages_raw() {
		$en = self::factory()->post->create();
		self::$model->post->set_language( $en, 'en' );

		$fr = self::factory()->post->create();
		self::$model->post->set_language( $fr, 'fr' );

		$de = self::factory()->post->create();
		self::$model->post->set_language( $de, 'de' );

		self::$model->post->save_translations( $en, compact( 'fr' ) );

		$this->frontend->links->curlang = self::$model->get_language( 'en' );
		$this->go_to( get_permalink( $en ) );

		// Raw with default arguments
		$args = array(
			'raw' => 1,
		);
		$arr = $this->switcher->the_languages( $this->frontend->links, $args );

		$this->assertCount( 3, $arr );
		$this->assertTrue( $arr['de']['no_translation'] );
		$this->assertTrue( $arr['en']['current_lang'] );
		$this->assertEquals( get_permalink( $en ), $arr['en']['url'] );
		$this->assertEquals( get_permalink( $fr ), $arr['fr']['url'] );
		$this->assertEquals( home_url( '?lang=de' ), $arr['de']['url'] ); // No translation
		$this->assertEquals( 'English', $arr['en']['name'] );
		$this->assertEquals( 'en-US', $arr['en']['locale'] );
		$this->assertEquals( 'en', $arr['en']['slug'] );
		$this->assertEquals( plugins_url( '/flags/us.png', POLYLANG_FILE ), $arr['en']['flag'] );

		// Other arguments
		$args = array_merge(
			$args,
			array(
				'force_home'             => 1,
				'hide_current'           => 1,
				'hide_if_no_translation' => 1,
				'display_names_as'       => 'slug',
			)
		);
		$arr = $this->switcher->the_languages( $this->frontend->links, $args );

		$this->assertCount( 1, $arr ); // Only fr in the array
		$this->assertEquals( home_url( '?lang=fr' ), $arr['fr']['url'] ); // force_home
		$this->assertEquals( 'fr', $arr['fr']['name'] ); // display_name_as

		$this->go_to( home_url( '/' ) );

		// Post_id
		$args = array(
			'raw'     => 1,
			'post_id' => $en,
		);
		$arr = $this->switcher->the_languages( $this->frontend->links, $args );
		$this->assertEquals( get_permalink( $fr ), $arr['fr']['url'] );
	}

	/**
	 *  Very basic tests for the switcher as list
	 */
	public function test_list() {
		$en = self::factory()->post->create();
		self::$model->post->set_language( $en, 'en' );

		$fr = self::factory()->post->create();
		self::$model->post->set_language( $fr, 'fr' );

		self::$model->post->save_translations( $en, compact( 'fr' ) );

		self::$model->clean_languages_cache(); // FIXME for some reason, I need to clear the cache to get an exact count

		$this->frontend->links->curlang = self::$model->get_language( 'en' );
		$this->go_to( get_permalink( $en ) );

		$args = array( 'echo' => 0 );
		$switcher = $this->switcher->the_languages( $this->frontend->links, $args );
		$doc = new DomDocument();
		$doc->loadHTML( $switcher );
		$xpath = new DOMXpath( $doc );

		$a = $xpath->query( '//li/a[@lang="en-US"]' );
		$this->assertEquals( get_permalink( $en ), $a->item( 0 )->getAttribute( 'href' ) );

		$a = $xpath->query( '//li/a[@lang="fr-FR"]' );
		$this->assertEquals( get_permalink( $fr ), $a->item( 0 )->getAttribute( 'href' ) );

		// Test echo option
		$args = array( 'echo' => 1 );
		ob_start();
		$this->switcher->the_languages( $this->frontend->links, $args );
		$this->assertNotEmpty( ob_get_clean() );

		// Bug fixed in 2.6.3: No span when showing only flags
		$args = array( 'show_names' => 0, 'show_flags' => 1, 'echo' => 0 );
		$switcher = $this->switcher->the_languages( $this->frontend->links, $args );
		$doc = new DomDocument();
		$doc->loadHTML( $switcher );
		$xpath = new DOMXpath( $doc );

		$this->assertEmpty( $xpath->query( '//li/a[@lang="en-US"]/span' )->length );

		// A span is used when showing names and flags
		$args = array( 'show_names' => 1, 'show_flags' => 1, 'echo' => 0 );
		$switcher = $this->switcher->the_languages( $this->frontend->links, $args );
		$doc = new DomDocument();
		$doc->loadHTML( $switcher );
		$xpath = new DOMXpath( $doc );

		$this->assertEquals( 'English', $xpath->query( '//li/a[@lang="en-US"]/span' )->item( 0 )->nodeValue );

		// Bug fixed in 2.6.10.
		$args = array( 'hide_current' => 1, 'echo' => 0 );
		$switcher = $this->switcher->the_languages( $this->frontend->links, $args );
		$doc = new DomDocument();
		$doc->loadHTML( $switcher );
		$xpath = new DOMXpath( $doc );

		$this->assertNotFalse( strpos( $xpath->query( '//li' )->item( 0 )->getAttribute( 'class' ), 'lang-item-first' ) );
	}

	/**
	 * Very basic tests for the switcher as dropdown
	 */
	public function test_dropdown() {
		$en = self::factory()->post->create();
		self::$model->post->set_language( $en, 'en' );

		$fr = self::factory()->post->create();
		self::$model->post->set_language( $fr, 'fr' );

		self::$model->post->save_translations( $en, compact( 'fr' ) );

		self::$model->clean_languages_cache(); // FIXME for some reason, I need to clear the cache to get an exact count

		$this->frontend->links->curlang = self::$model->get_language( 'en' );
		$this->go_to( get_permalink( $en ) );

		$args = array(
			'dropdown' => 1,
			'echo'     => 0,
		);
		$switcher = $this->switcher->the_languages( $this->frontend->links, $args );
		$doc = new DomDocument();
		$doc->loadHTML( '<?xml encoding="UTF-8">' . $switcher );
		$xpath = new DOMXpath( $doc );

		$option = $xpath->query( '//select/option[.="English"]' );
		$this->assertEquals( 'selected', $option->item( 0 )->getAttribute( 'selected' ) );
		$this->assertNotEmpty( $xpath->query( '//select/option[.="Français"]' )->length );
		$this->assertNotEmpty( $xpath->query( '//script' )->length );
		$lang_attributes = $xpath->query( '//select/option/@lang' );
		$this->assertEquals( 'en-US', $lang_attributes->item( 0 )->value );
		$this->assertEquals( 'fr-FR', $lang_attributes->item( 1 )->value );
	}

	public function test_with_hide_if_no_translation_option_in_admin_context() {
		$links_model = self::$model->get_links_model();
		$this->pll_admin = new PLL_Admin( $links_model );
		$this->pll_admin->init();

		$en = self::factory()->post->create();
		self::$model->post->set_language( $en, 'en' );

		$fr = self::factory()->post->create();
		self::$model->post->set_language( $fr, 'fr' );

		self::$model->clean_languages_cache(); // FIXME for some reason, I need to clear the cache to get an exact count

		$args = array(
			'hide_if_no_translation' => 1,
			'echo'                   => 0,
		);
		$switcher = $this->switcher->the_languages( $this->pll_admin->links, $args );

		$this->assertNotEmpty( $switcher );

		$doc = new DomDocument();
		$doc->loadHTML( $switcher );
		$xpath = new DOMXpath( $doc );

		$a = $xpath->query( '//li/a[@lang="en-US"]' );
		$this->assertEquals( $this->pll_admin->links->get_home_url( self::$model->get_language( 'en' ) ), $a->item( 0 )->getAttribute( 'href' ) );

		$a = $xpath->query( '//li/a[@lang="fr-FR"]' );
		$this->assertEquals( $this->pll_admin->links->get_home_url( self::$model->get_language( 'fr' ) ), $a->item( 0 )->getAttribute( 'href' ) );
	}

	/**
	 * @ticket #1890
	 * @see https://github.com/polylang/polylang-pro/issues/1890.
	 */
	public function test_flags_a11y_without_names_displayed() {
		$args = array(
			'show_flags'    => 1,
			'show_names'    => 0, // Don't display names.
			'echo'          => 0,
			'hide_if_empty' => 0,
		);
		$this->frontend->links->curlang = self::$model->get_language( 'en' );
		$switcher = $this->switcher->the_languages( $this->frontend->links, $args );

		$this->assertNotEmpty( $switcher );

		$doc  = new DomDocument();
		$html = '<?xml encoding="UTF-8">' . $switcher; // Ensure encoding is correct.
		$doc->loadHTML( $html );
		$xpath = new DOMXpath( $doc );

		$a = $xpath->query( '//li/a[@lang="en-US"]' );
		$this->assertSame( 1, $a->length, 'There should be only one child node.' );
		$this->assertSame( 'English', $a->item( 0 )->childNodes->item( 0 )->getAttribute( 'alt' ), 'Alternative text value should be "English".' );

		$a = $xpath->query( '//li/a[@lang="fr-FR"]' );
		$this->assertSame( 1, $a->length, 'There should be only one child node.' );
		$this->assertSame( 'Français', $a->item( 0 )->childNodes->item( 0 )->getAttribute( 'alt' ), 'Alternative text value should be "Français".' );
	}

	/**
	 * @ticket #1890
	 * @see https://github.com/polylang/polylang-pro/issues/1890.
	 */
	public function test_flags_a11y_with_names_displayed() {
		$args = array(
			'show_flags'    => 1,
			'show_names'    => 1, // Display names.
			'echo'          => 0,
			'hide_if_empty' => 0,
		);
		$this->frontend->links->curlang = self::$model->get_language( 'en' );
		$switcher = $this->switcher->the_languages( $this->frontend->links, $args );

		$this->assertNotEmpty( $switcher );

		$doc  = new DomDocument();
		$html = '<?xml encoding="UTF-8">' . $switcher; // Ensure encoding is correct.
		$doc->loadHTML( $html );
		$xpath = new DOMXpath( $doc );

		$a = $xpath->query( '//li/a[@lang="en-US"]' );
		$this->assertSame( 1, $a->length, 'There should be only one child node.' );
		$this->assertEmpty( $a->item( 0 )->childNodes->item( 0 )->getAttribute( 'alt' ), 'There should be no alternative texts.' );

		$a = $xpath->query( '//li/a[@lang="fr-FR"]' );
		$this->assertSame( 1, $a->length, 'There should be only one child node.' );
		$this->assertEmpty( $a->item( 0 )->childNodes->item( 0 )->getAttribute( 'alt' ), 'There should be no alternative texts.' );
	}
}
