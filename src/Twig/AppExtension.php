<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

/**
 * Deining custom Twig extension
 * @package App\Twig
 */
class AppExtension extends AbstractExtension implements GlobalsInterface
{

    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     *
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return [
          new TwigFilter('price', [$this, 'priceFilter'])
        ];
    }

    public function getGlobals(): array
    {
        return [
          'locale' => $this->locale
        ];
    }

    /**
     * Return price in a given format
     * @param $number
     * @return string
     */
    public function priceFilter($number)
    {
        return '$' . number_format($number, 2, '.', ',');
    }
}