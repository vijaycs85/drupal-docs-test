<?php

namespace Drupal\Tests\mymodule\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test basic functionality of My Module.
 *
 * @group mymodule
 */
class BasicTestCase extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    // Module(s) for core functionality.
    'node',
    'views',

    // This custom module.
    'mymodule',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Make sure to complete the normal setup steps first.
    parent::setUp();


    // Set the front page to "node".
    \Drupal::configFactory()
      ->getEditable('system.site')
      ->set('page.front', '/node')
      ->save(TRUE);

    // Create Basic page and Article node types.
    $this->drupalCreateContentType([
      'type' => 'page',
      'name' => 'Basic page',
      'display_submitted' => FALSE,
    ]);
    $web_user = $this->drupalCreateUser(['edit own page content', 'create page content', 'administer menu']);
    $this->drupalLogin($web_user);
  }

  /**
   * Make sure the site still works. For now just check the front page.
   */
  public function testTheSiteStillWorks() {
    // Load the front page.
    $this->drupalGet('<front>');

    // Confirm that the site didn't throw a server error or something else.
    $this->assertSession()->statusCodeEquals(200);

    // Confirm that the front page contains the standard text.
    $this->assertText('Welcome to Drupal');
  }

  /**
   * Tests create/view of a page node.
   */
  public function testNode() {
    // Add field values.
    $edit = [
      'title[0][value]' => $this->randomMachineName(8),
      'body[0][value]' => $this->randomMachineName(16),
    ];
    // Submit the page form to create a new page.
    $this->drupalPostForm('node/add/page', $edit, 'Save');
    $this->assertSession()->statusCodeEquals(200);

    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($edit['title[0][value]']);
    $this->assertSession()->pageTextContains($edit['body[0][value]']);
  }

}

