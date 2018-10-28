<?php

/**
 * @property \Faker\Generator $faker
 * @property integer $index
 */

$faker->addProvider(new Faker\Provider\Internet($faker));

return [
    "post_id"        => ($index + 1),
    "post_link_type" => \app\models\post\PostLinkType::GITHUB,
    "link"           => $faker->url,
];