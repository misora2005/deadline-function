<?php
/**
 * @package Deadline_Function
 * @version 1.0.0
 */
/*
Plugin Name: Dadline Function
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: Webシステム開発2025の毎週火曜日23:59までの課題締切カウントダウンを表示するテスト用プラグインです。
Author: 23T338
Version: 1.0.0
Author URI: http://ma.tt/
*/

// Do not load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

function hello_dolly_get_lyric() {
	/** These are the lyrics to Hello Dolly */
	$lyrics = "Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
I feel the room swayin'
While the band's playin'
One of our old favorite songs from way back when
So, take her wrap, fellas
Dolly, never go away again
Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
I feel the room swayin'
While the band's playin'
One of our old favorite songs from way back when
So, golly, gee, fellas
Have a little faith in me, fellas
Dolly, never go away
Promise, you'll never go away
Dolly'll never go away again";

	// Here we split it into lines.
	$lyrics = explode( "\n", $lyrics );

	// And then randomly choose a line.
	return wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
}

// This just echoes the chosen line, we'll position it later.
function hello_dolly() {
	$now = new DateTime('now', wp_timezone());
	$today_weekday = (int) $now->format('w'); // 0=Sun, 1=Mon, ..., 2=Tue
	$today_midnight = clone $now;
	$today_midnight->setTime(0, 0, 0);

	// 締切の火曜 23:59:59
	if ($today_weekday <= 2) {
		$deadline = new DateTime('this week tuesday 23:59:59', wp_timezone());
	} else {
		$deadline = new DateTime('next week tuesday 23:59:59', wp_timezone());
	}

	$interval = $now->diff($deadline);

	$days    = (int) $interval->format('%a');
	$hours   = (int) $interval->format('%h');

	// 今日が締切日の場合（火曜日）
	if ($days === 0) {
		$message = "課題締め切りは今日です！あと{$hours}時間!!";
	} else {
		$message = "課題締め切りまであと{$days}日と{$hours}時間!!";
	}

	printf(
		'<p id="dolly"><span class="screen-reader-text">%s </span><span dir="ltr">%s</span></p>',
		__( 'Weekly deadline countdown:' ),
		esc_html($message)
	);
}

// Now we set that function up to execute when the admin_notices action is called.
add_action( 'admin_notices', 'hello_dolly' );

// We need some CSS to position the paragraph.
function dolly_css() {
	echo "
	<style type='text/css'>
	#dolly {
		float: right;
		padding: 5px 10px;
		margin: 0;
		font-size: 12px;
		line-height: 1.6666;
	}
	.rtl #dolly {
		float: left;
	}
	.block-editor-page #dolly {
		display: none;
	}
	@media screen and (max-width: 782px) {
		#dolly,
		.rtl #dolly {
			float: none;
			padding-left: 0;
			padding-right: 0;
		}
	}
	</style>
	";
}

add_action( 'admin_head', 'dolly_css' );
