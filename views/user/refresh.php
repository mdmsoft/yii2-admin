<?php

foreach ($users as $user) {
	echo $user->id.' : '.$user->password_hash.'<br/>';
}
