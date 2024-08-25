<?php

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

function getNavigationLink(array $item): string {
  $prefix = URL::to('/') . '/' . app()->getLocale() . '/';
  $data = $item['data'];

  if($item['type'] == 'page' && isset($data['page_slug'])) {
    if($data['page_slug'] == '/') {
      return $prefix;
    }

    return $prefix . $data['page_slug'];
  } else if (preg_match('/^https?:\/\//', $data['url']) || preg_match('/^\#/', $data['url'])) {
    return $data['url'];
  } else {
    return $prefix . trim($data['url'], '/');
  }
}

function isNavigationActive(array $item, \Z3d0X\FilamentFabricator\Models\Page $page): bool
{
  $data = $item['data'];

  if($item['type'] == 'page' && isset($data['page_slug'])) {
    return $data['page_slug'] == $page->slug;
  } else if (!preg_match('/^https?:\/\//', $data['url']) || preg_match('/^\#/', $data['url'])) {
    return trim($data['url'], '/') == trim($page->slug, '/');
  }

  return false;
}

function internalLink($slug): string
{
  return URL::to('/') . '/' . app()->getLocale() . '/' . $slug;
}

function generateUniqueSlug(string $originalSlug): string
{
  return $originalSlug . '-' . rand(1000000, 10000000);
}