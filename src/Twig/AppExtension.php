<?php

namespace App\Twig;

use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pluralize', [$this, 'check']),
            new TwigFunction('createOrUpdate', [$this, 'checkDate'])
        ];
    }

    public function check(int $count, string $singular, ?string $plural = null): String
    {
        $plural ??= $singular."s";
        return $count." ".($count == 1 ? $singular : $plural);
    }

    public function checkDate(DateTimeImmutable $creation, DateTimeImmutable $edition): DateTimeImmutable
    {
        return ($creation == $edition) ? $creation : $edition;
    }
}
