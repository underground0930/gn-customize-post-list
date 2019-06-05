=== Plugin Name ===
Contributors: wentbook
Tags: admin
Requires at least: 5.0
Tested up to: 5.2.1
Requires PHP: 5.6.20
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

*[EN]*
You can customize the display items of the article list.

*[JA]*
* 「投稿」、「カスタム投稿」、「固定ページ」の記事一覧ページの表示項目をカスタマイズ出来るWordressプラグインです。
* 投稿画面一覧にそれぞれのタクソノミーが持つタームでの絞り込みのプルダウンを付加します。

----

* プラグインを有効化すると、「設定」メニューに「gn-customize-post-list」の項目が追加されるので、そちらから編集を行ってください。
* 編集ページに行くと、「投稿」、「固定ページ」、今、設定している「カスタム投稿」の全ての一覧画面の表示項目のlistが表示されています。
* 自分が表示させたい項目を「type」のセレクトタグで変更してください。
* １つの投稿タイプにつき６項目まで設定出来ます。
* 「add row」で項目の追加、「delete」で項目の削除、「reset」で項目を元に戻すことが出来ます。
* ページ下部の「update」ボタンで更新が出来ます。
* 同一投稿タイプ内で、重複した項目は使用出来ません。(custom_field_text,custom_field_img, taxonomyは除く)。
* custom_field_text, custom_field_img, taxonomy の「label」は表示させたい「見出し」を、入力してください。
* custom_field_text, custom_field_img, taxonomy の「slug」は表示させたい項目の「スラッグ」を入れてください。値が間違っていて存在しない場合、何も表示されません 。
* taxonomyのslugは「edit-tags.php?taxonomy=●●●●●●」の部分のものを入力してください。
* custom_field_text は、フィールドタイプが「テキスト」のものを表示させるカスタムフィールドに対応しています。
* custom_field_img は、フィールドタイプが「画像」のものを表示させるカスタムフィールドに対応しています。
* custom_field_text, custom_field_img, taxonomy の「label」「slug」は 1文字以上30字未満で入力してください。

== Installation ==

*[EN]*

1. Upload pluginFiles to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

*[JA]*
1. プラグインのファイルディレクトリを `/wp-content/plugins/` ディレクトリにアップロードしてください。
2. プラグインメニューからプラグインの有効化をしてください

== Screenshots ==

1. /assets/screenshot-1.png
2. /assets/screenshot-2.png
3. /assets/screenshot-3.png

== Changelog ==

*[EN]*

= 1.0.0 =

*Release Date - 2019/6/2*
* initial version

*[JA]*

= 1.0.0 =

*リリース日 - 2019/6/2*
* 初期バージョン
