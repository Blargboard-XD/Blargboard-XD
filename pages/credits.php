<?php
if (!defined('BLARG')) die();

$credits = ('
	This board software was created by the following contributors, in no special order.<br>
	<ul>
		<li>Kawa — Originally created Acmlmboard XD ("ABXD")</li>
		<li>Dirabio — Contributed to ABXD 3.0, which parts of this board are based off of</li>
		<li>StapleButter — Created Blargboard (a fork of Acmlmboard XD)</li>
		<li>Maorninja322 — Created WorldWeb XD (a fork of Blargboard), coder</li>
		<li>JeDaYoshi — Ported all the Acmlmboard XD plugins to Blargboard, ported the RPG, coder</li>
		<li>Phase — Added compatibility to 5.7 MySQL databases, JSON ranksets, etc...</li>
		<li>MoonlightCapital / Super-toad 64 - (TBH: I don\'t know exactly what he did and what he didn\'t so I\'ll just leave it like this).</li>
		<li>Repflez - Fixed bugs, and made a private fork of Blargboard, which is going to get merged here soon.</li>
		<li>DankMemeItTheFrog - Made Instameme, and ported the Layout Maker, while adding some of his changes.</li>
		<li>LifeMushroom — Themes</li>
		<li>Everyone behind <a href="https://fortawesome.github.io/Font-Awesome/">Font Awesome</a>, <a href="https://jquery.com/">jQuery</a>, Smarty, and any other libraries this software uses</li>
	</ul>');

RenderTemplate('credits', array('credits' => $credits));
