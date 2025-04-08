<?php

declare(strict_types=1);

namespace Drupal\server_general\ThemeTrait;

use Drupal\Core\Url;

/**
 * Helper methods for rendering different "inner element" layouts such as cards.
 *
 * An inner element can be for example a card with an image, or a search result
 * with centered items. This trait should only be used by other traits in
 * ThemeTrait namespace.
 * You should not try to call this trait's methods directly from the Style guide
 * or PEVB, instead you should be calling the methods from a custom
 * ThemeTrait such as Drupal\server_general\ThemeTrait\InfoCardThemeTrait.
 *
 * @see \Drupal\server_general\ThemeTrait\InfoCardThemeTrait::buildElementInfoCard.
 */
trait InnerElementLayoutThemeTrait {

  /**
   * Build "Card" layout - the simplest one.
   *
   * @param array $items
   *   The elements as render array.
   * @param string|null $bg_color
   *   Optional; The background color. Allowed values are:
   *   - 'light-gray'.
   *   If NULL, a transparent background will be added.
   *
   * @return array
   *   Render array.
   */
  protected function buildInnerElementLayout(array $items, ?string $bg_color = NULL): array {
    return [
      '#theme' => 'server_theme_inner_element_layout',
      '#items' => $this->wrapContainerVerticalSpacing($items),
      '#bg_color' => $bg_color,
    ];
  }

  /**
   * Build "Centered card" layout.
   *
   * @param array $items
   *   The elements as render array.
   *
   * @return array
   *   Render array.
   */
  protected function buildInnerElementLayoutCentered(array $items): array {
    return [
      '#theme' => 'server_theme_inner_element_layout__centered',
      '#items' => $this->wrapContainerVerticalSpacing($items, 'center'),
    ];
  }
  /**
   * Build a Person Card.
   *
   * @param string $image_url
   *   The image Url.
   * @param string $name
   *   The name.
   * @param string|null $subtitle
   *   Optional; The subtitle (e.g. work title).
   * @param string $badge
   * The badge title.
   * @param string $email
   *   The email address.
   * @param string $phone
   *   The phone number.
   *
   * @return array
   *   The render array.
   */
  protected function buildInnerElementPersonCard(string $image_url, string $name, string $subtitle = NULL, string $badge = NULL, $email= NULL, $phone = NULL): array {
    $elements = [];
    $image = [
      '#theme' => 'image',
      '#uri' => $image_url,
      '#alt' => 'The image alt ' . $name,
      '#width' => 128,
    ];

    // Image markup.
    $image = $this->wrapRoundedCornersFull($image);
    $inner_elements[] = $this->wrapContainerBottomPadding($image);

    // Text elements in the cards.
    $element = $this->wrapTextFontWeight($name, 'medium');
    $element = $this->wrapTextResponsiveFontSize($element, 'sm');
    $element = $this->wrapTextCenter($element);
    $text_elements[] = $this->wrapTextColor($element, 'darker-gray');

    if ($subtitle) {
      $element = $this->wrapTextResponsiveFontSize($subtitle, 'sm');
      $element = $this->wrapTextCenter($element);
      $text_elements[] = $this->wrapTextColor($element, 'gray');
    }
    if ($badge) {
      $badge = $this->wrapTextResponsiveFontSize($badge, 'sm');
      $badge = $this->wrapRoundedCornersBadge($badge, 'light-green');
      $text_elements[] = $this->wrapTextColor($badge, 'dark-green');
    }
    // Wrap all the text elements together.
    $inner_elements[] = $this->wrapCardText($text_elements);
    $elements[] = $this->wrapContainerVerticalSpacingCard($inner_elements, 'center');

    // Build Contact CTAs.
    $email_text = $this->getCardCtaText($this->t('Email'));
    $phone_text = $this->getCardCtaText($this->t('Phone'));
    $email_element = ['url' => "mailto::{$email}", 'text' => $email_text ];
    $phone_element = ['url' => "mailto::{$phone}", 'text' => $phone_text ];
    $elements[] = $this->buildInnerElementContactCta($email_element, $phone_element);
    return $this->buildInnerElementLayoutCard($elements, 'white');
  }

  /**
   * Wrap person card text with styles.
   *
   * @param array $element
   *   The image render array.
   *
   * @return array
   *   Render array.
   */
  protected function wrapCardText(array $element): array {
    $element = $this->filterEmptyElements($element);
    if (empty($element)) {
      return [];
    }

    return [
      '#theme' => 'server_theme_wrap_card_text',
      '#element' => $element,
    ];
  }

  /**
   * Wrap an element for card with fix width and custom spacing.
   *
   * @param array $element
   *   Render array.
   * @param string $align
   *   Determine if flex should also have an alignment. Possible values are
   *   `start`, `center`, `end` or NULL to have no change.
   *
   * @return array
   *   Render array.
   */
  protected function wrapContainerVerticalSpacingCard(array $element, string $align = NULL): array {
    $element = $this->filterEmptyElements($element);
    if (empty($element)) {
      // Element is empty, so no need to wrap it.
      return [];
    }

    return [
      '#theme' => 'server_theme_container_vertical_spacing_card',
      '#items' => $element,
      '#align' => $align,
    ];
  }


  /**
   * Wrap an element with a tiny vertical spacing (8px).
   *
   * @param array $element
   *   Render array.
   * @param string $align
   *   Determine if flex should also have an alignment. Possible values are
   *   `start`, `center`, `end` or NULL to have no change.
   *
   * @return array
   *   Render array.
   */
  protected function buildInnerElementContactCta($email = [], $phone = []): array {
    return [
      '#theme' => 'server_theme_inner_element_contact_cta',
      '#email' => $email,
      '#phone' => $phone,
    ];
  }


  /**
   * Build "Centered card" layout.
   *
   * @param array $items
   *   The elements as render array.
   * @param array $bg_color
   *   Optional; The background color. Allowed values are:
   *    - 'light-gray'.
   *    - 'light-green'.
   *    - 'white'.
   *
   * @return array
   *   Render array.
   */
  protected function buildInnerElementLayoutCard(array $items, string $bg_color = NULL): array {
    return [
      '#theme' => 'server_theme_inner_element_layout__card',
      '#items' => $items,
      '#bg_color' => $bg_color,
    ];
  }


  /**
   * Build "Card with image" layout.
   *
   * This is the "base" helper method for rendering a card with image. Specific
   * cards may implement own helper methods, that will use this one.
   *
   * @param \Drupal\Core\Url $url
   *   The URL to link to.
   * @param array $image
   *   The image render array.
   * @param array $items
   *   The rest of the items' render array.
   *
   * @return array
   *   Render array.
   */
  protected function buildInnerElementLayoutWithImage(Url $url, array $image, array $items): array {
    return [
      '#theme' => 'server_theme_inner_element_layout__with_image',
      '#image' => $image,
      '#url' => $url,
      '#items' => $this->wrapContainerVerticalSpacing($items),
    ];
  }

  /**
   * Build "Card with image horizontal" layout.
   *
   * @param \Drupal\Core\Url $url
   *   The URL to link to.
   * @param array $image
   *   The image render array.
   * @param array $items
   *   The rest of the items' render array.
   *
   * @return array
   *   Render array.
   */
  protected function buildInnerElementLayoutWithImageHorizontal(Url $url, array $image, array $items): array {
    return [
      '#theme' => 'server_theme_inner_element_layout__with_image_horizontal',
      '#image' => $image,
      '#url' => $url,
      '#items' => $this->wrapContainerVerticalSpacing($items),
    ];
  }

}
