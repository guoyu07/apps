<?php

namespace APPS\user\oauth2;

\Ko_Web_Route::VGet('index', function () {
	MApi::oauth2login('weibo');
});
