<?php

use Illuminate\Support\Facades\URL;

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

function internalLink($slug): string
{
  return URL::to('/') . '/' . app()->getLocale() . '/' . $slug;
}