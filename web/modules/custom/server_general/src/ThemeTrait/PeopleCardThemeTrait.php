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

  /**
   * Get Person Card element.
   *
   * @return array
   *   Render array.
   */
  protected function getPersonCard(): array {
    $person = [
      'name' => 'Jon Doe',
      'email' => 'JonDoe@example.com',
      'subtitle' => 'Paradigm Representative',
      'badge' => 'Admin',
      'phone' => "(555) 555-1234",
    ];
    $items[] = $this->buildInnerElementPersonCard(
      $this->getPlaceholderPersonImage(128),
      $person['name'],
      $person['subtitle'],
      $person['badge'],
      $person['email'],
      $person['phone'],
    );
    $cards = $this->buildElementPeopleCards(
      $this->getRandomTitle(),
      [],
      $items,
    );

    // Add gray background for prominent box shadow.
    return $this->wrapContainerWide($cards, 'light-gray');
  }

  /**
   * Get People Card element.
   *
   * @return array
   *   Render array.
   */
  protected function getPeopleCards(): array {
    $items = [];
    $people = [
      [
        'name' => 'Jane Cooper',
        'email' => 'janeCooper@example.com',
        'phone' => "(555) 555-1234",
        'subtitle' => 'Paradigm Representative',
        'badge' => 'Admin',
      ],
      [
        'name' => 'Smith Allen',
        'email' => 'SmithAllen@example.com',
        'phone' => "(555) 555-1234",
        'badge' => 'Admin',
      ],
      [
        'name' => 'Rick Morty',
        'email' => 'RickMorty@example.com',
        'phone' => "(555) 555-1234",
        'subtitle' => 'Paradigm Representative',
        'badge' => 'Admin',
      ],
      [
        'name' => 'David Bowie',
        'email' => 'DavidBowie@example.com',
        'phone' => "(555) 555-1234",
        'subtitle' => 'Paradigm Representative',
        'badge' => 'Admin',
      ],
      [
        'name' => 'Smith John',
        'email' => 'SmithAllen@example.com',
        'phone' => "(555) 555-1234",
        'subtitle' => 'Paradigm Representative',
      ],
      [
        'name' => 'Jon Doe',
        'email' => 'JonDoe@example.com',
        'phone' => "(555) 555-1234",
        'subtitle' => 'Paradigm Representative',
      ],
      [
        'name' => 'David Bowie',
        'email' => 'DavidBowie@example.com',
        'phone' => "(555) 555-1234",
        'subtitle' => 'Paradigm Representative',
      ],
      [
        'name' => 'Rick Morty',
        'email' => 'RickMorty@example.com',
        'phone' => "(555) 555-1234",
        'subtitle' => 'Paradigm Representative',
      ],
      [
        'name' => 'Smith Locke',
        'email' => 'SmithAllen@example.com',
        'phone' => "(555) 555-1234",
        'subtitle' => 'Paradigm Representative',
      ],
      [
        'name' => 'Jon Doom',
        'email' => 'JonDoe@example.com',
        'phone' => "(555) 555-1234",
        'subtitle' => 'Paradigm Representative',
      ],
    ];
    foreach ($people as $person) {
      $items[] = $this->buildInnerElementPersonCard(
        $this->getPlaceholderPersonImage(128),
        $person['name'],
        $person['subtitle'] ?? '',
        $person['badge'] ?? '',
        $person['email'],
        $person['phone'],
      );
    }

    $cards = $this->buildElementPeopleCards(
      $this->getRandomTitle(),
      [],
      $items,
    );

    // Gray background for prominent box shadow.
    return $this->wrapContainerWide($cards, 'light-gray');
  }


}
