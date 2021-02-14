<?php

namespace App\Core\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MailtoExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('mailto', [$this, 'mailto'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    public function mailto(string $text)
    {
        if (preg_match_all('/[\p{L}0-9_.-]+@[0-9\p{L}.-]+\.[a-z.]{2,6}\b/u', $text, $mails)) {
            foreach ($mails[0] as $mail) {
                $text = str_replace($mail, '<a href="mailto:'.$mail.'">'.$mail.'</a>', $text);
            }
        }

        return $text;
    }
}
