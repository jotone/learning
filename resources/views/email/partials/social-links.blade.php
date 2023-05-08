<div class="social-links">
  @foreach($social_links as $link)
    <div class="link">
      <a href="{{ $link->url }}">
        <img src="{{ asset('/images/social-media/'. $link->type . '.png') }}" alt="">
      </a>
    </div>
  @endforeach
</div>