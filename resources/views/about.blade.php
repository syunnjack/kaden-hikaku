@extends('layouts.app')

@section('title', 'このサイトについて | ' . config('app.name'))
@section('description', config('app.name') . 'の運営方針、データの出典、口コミの取り扱いについて説明しています。')

@section('content')
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('kaden.index') }}">{{ config('app.name') }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">このサイトについて</li>
    </ol>
  </nav>

  <h1>このサイトについて</h1>

  <section class="mb-4">
    <h2 class="h5">サイトの目的</h2>
    <p>
      「{{ config('app.name') }}」は、家電・ガジェットをジャンルやキーワードから検索できるサイトです。
      価格情報だけでなく、実際に使った方の口コミもあわせて確認できるようにしています。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h5">掲載データの出典</h2>
    <p>
      掲載している商品情報（名称・画像URL・価格・購入リンク等）は、楽天市場が提供する
      <a href="https://webservice.rakuten.co.jp/" target="_blank" rel="noopener noreferrer">楽天ウェブサービス</a>
      のAPIを通じて取得しており、随時最新の情報に更新されます。購入は楽天市場のサイトで行われます。
      価格・在庫状況は変更される場合があるため、購入前に必ず楽天市場の商品ページでご確認ください。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h5">口コミについて</h2>
    <p>
      口コミは、どなたでもログイン不要で投稿できます。投稿内容は運営による事前確認を行わず即時公開されますが、
      不適切な投稿を発見された場合は内容を精査のうえ対応します。口コミはあくまで投稿者個人の感想であり、
      当サイトが内容の正確性を保証するものではありません。
    </p>
  </section>
</div>
@endsection
