<?php

namespace app\models\post;

/**
 * Class PostStatus
 *
 * @package app\models\post
 */
class PostStatus extends PostStatusBase
{
	const DRAFT       = 1;
	const UNPUBLISHED = 2;
	const PUBLISHED   = 3;
	const ARCHIVED    = 4;
}