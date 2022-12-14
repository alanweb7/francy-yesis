<?php
namespace Acme\Widget;
use Adianti\Widget\Base\TElement;
class ProgressBar extends TElement
{
    public function __construct($percentage, $message)
    {
        parent::__construct('div');
        $this->{'class'} = 'progress';
        
        $div = new TElement('div');
        $div->{'class'} = 'progress-bar';
        $div->{'role'}  = 'progressbar';
        $div->{'aria-valuenow'} = $percentage;
        $div->{'aria-valuemin'} = '0';
        $div->{'aria-valuemax'} = '100';
        $div->{'style'} = "width: {$percentage}%;";
        
        $span = new TElement('span');
        $span->{'class'} = 'sr-only';
        $span->add($message);
        $div->add($span);
        
        parent::add($div);
    }
}