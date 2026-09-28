{{--
  A portfolio photo as a <picture>: resized WebP copies via srcset when they
  exist (browsers pick the smallest one that fills `sizes` at their pixel
  density), the stored master as the <img> fallback otherwise.
  width/height let the browser reserve the photo's space before it loads;
  CSS still sizes the image, so the attributes only supply the aspect ratio.
--}}
@props(['item', 'sizes', 'pictureClass' => 'block'])
@php($srcset = $item->srcset())
<picture class="{{ $pictureClass }}">
  @if($srcset)
    <source type="image/webp" srcset="{{ $srcset }}" sizes="{{ $sizes }}">
  @endif
  <img src="{{ $item->imageUrl() }}" alt="{{ $item->displayAlt() }}" @if($item->hasDimensions())width="{{ $item->width }}" height="{{ $item->height }}" @endif{{ $attributes->merge(['loading' => 'lazy', 'decoding' => 'async']) }}>
</picture>
