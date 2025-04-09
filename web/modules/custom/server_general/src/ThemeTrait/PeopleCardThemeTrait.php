<?php

declare(strict_types=1);

namespace Drupal\server_general\ThemeTrait;

/**
 * Helper methods for rendering Info Card elements.
 */
trait PeopleCardThemeTrait {

  use ElementLayoutThemeTrait;
  use ElementWrapThemeTrait;
  use CardThemeTrait;
  use InnerElementLayoutThemeTrait;

  /**
   * Build People Cards element.
   *
   * @param string $title
   *   The title.
   * @param array $body
   *   The body render array.
   * @param array $items
   *   Person card Items.
   *
   * @return array
   *   The render array.
   */
  protected function buildElementPeopleCards(string $title, array $body, array $items): array {
    return $this->buildElementLayoutTitleBodyAndItems(
      $title,
      $body,
      $this->buildCards($items),
    );
  }

}
