<?php
/**
 * Created by PhpStorm.
 * User: Raqibul Hasan Moon
 * Date: 14/07/2023
 * Time: 10:35 PM
 */

function getPromptPath($name): string
{
    return asset('uploads/prompts/' . $name . '.mp3');
}
