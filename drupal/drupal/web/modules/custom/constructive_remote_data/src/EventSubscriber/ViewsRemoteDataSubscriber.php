<?php

declare(strict_types=1);

namespace Drupal\constructive_remote_data\EventSubscriber;

use Drupal\node\Entity\Node;
use Drupal\views\ResultRow;
use Drupal\views_remote_data\Events\RemoteDataLoadEntitiesEvent;
use Drupal\views_remote_data\Events\RemoteDataQueryEvent;
use Drupal\constructive_remote_data\ConstructiveApi;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for populating remote values in views.
 */
final class ViewsRemoteDataSubscriber implements EventSubscriberInterface {

  /**
   * The ConstructiveApi definition.
   *
   * @var \Drupal\constructive_remote_data\ConstructiveApi
   */
  protected ConstructiveApi $constructiveApi;

  /**
   * Constructs a new ViewsRemoteDataSubscriber object.
   *
   * @param \Drupal\constructive_remote_data\ConstructiveApi $constructive_api
   *   The constructive api.
   */
  public function __construct(ConstructiveApi $constructive_api) {
    $this->constructiveApi = $constructive_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      RemoteDataQueryEvent::class => 'onQuery',
      RemoteDataLoadEntitiesEvent::class => 'onLoadEntities',
    ];
  }

  /**
   * Subscribes to populate entities against the views results.
   *
   * @param \Drupal\views_remote_data\Events\RemoteDataLoadEntitiesEvent $event
   *   The remote data load entities event.
   *
   * @return void
   *   Method does not return.
   */
  public function onLoadEntities(RemoteDataLoadEntitiesEvent $event): void {
    $supported_bases = [
      'constructive_remote_data',
    ];
    $base_tables = array_keys($event->getView()->getBaseTables());
    if (count(array_intersect($supported_bases, $base_tables)) > 0) {
      $posts = $this->constructiveApi->getPosts();
      foreach ($event->getResults() as $index => $result) {
        /*
        assert(property_exists($result, 'id'));
        assert(property_exists($result, 'title'));
        assert(property_exists($result, 'published'));
        assert(property_exists($result, 'body'));

        $post = array_filter($posts, )
        /**/
        $result->_entity = Node::create([
          'title' => $posts[0]['title'],
          'body' => $posts[0]['body'],
          'field_published' => $posts[0]['published'],
          'type' => 'remote_post',
        ]);

      }
    }
  }

  /**
   * Subscribes to populate the views results.
   *
   * @param \Drupal\views_remote_data\Events\RemoteDataQueryEvent $event
   *  The event.
   *
   * @return void
   *   Method does not return.
   */
  public function onQuery(RemoteDataQueryEvent $event): void {
    $supported_bases = [
      'constructive_remote_data',
    ];
    $base_tables = array_keys($event->getView()->getBaseTables());
    if (count(array_intersect($supported_bases, $base_tables)) > 0) {
      $posts = $this->constructiveApi->getPosts();
      foreach ($posts['results'] as $post) {
        $event->addResult(new ResultRow($post));
      }
    }
  }

}
