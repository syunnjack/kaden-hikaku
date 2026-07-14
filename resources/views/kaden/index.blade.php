@extends('layouts.app')

@section('title', config('app.name') . ' | 価格と口コミで家電・ガジェットを比較')
@section('description', '家電・ガジェットをジャンル・キーワードから検索できるサイトです。楽天市場の価格情報に加えて、実際に使った人の口コミも確認できます。')

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => config('app.name'),
    'url' => url('/'),
    'description' => '家電・ガジェットをジャンル・キーワードから検索できる比較情報サイト。',
    'inLanguage' => 'ja',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')
<div class="container">
  <h1>家電・ガジェットを探す</h1>
  <p class="text-muted">
    {{ config('app.name') }}では、ジャンルやキーワードから家電・ガジェットを検索できます。
    楽天市場の価格情報に加えて、実際に使った人の口コミも確認できます。
  </p>

  <form method="GET" action="{{ route('kaden.search') }}" class="row g-2 mb-4">
    <div class="col-9 col-md-10">
      <input type="text" name="keyword" class="form-control" placeholder="例：ドラム式洗濯機、ワイヤレスイヤホンなど" required>
    </div>
    <div class="col-3 col-md-2">
      <button type="submit" class="btn btn-primary w-100">検索</button>
    </div>
  </form>

  <h2 class="h5">人気ジャンルから探す</h2>
  <div class="row row-cols-2 row-cols-md-4 g-2 mt-1">
    @foreach ($categories as $category)
      <div class="col">
        <a href="{{ route('kaden.search', ['keyword' => $category]) }}" class="btn btn-outline-primary w-100">
          {{ $category }}
        </a>
      </div>
    @endforeach
  </div>

  <section class="mt-5 pt-4 border-top">
    <h2 class="h5">このサイトの特徴</h2>
    <p class="text-muted small">
      各商品ページでは、楽天市場での購入リンクだけでなく、実際に使った人のリアルな口コミも確認できます。
      詳しくは<a href="{{ route('about') }}">このサイトについて</a>をご覧ください。
    </p>
  </section>
</div>
@endsection
