<?php
namespace Excel\Widget;
use Adianti\Widget\Base\TElement;
class PhpToExcel extends TElement
{
    public function __construct($percentage, $message)
    {
        parent::__construct('div');
        $this->{'class'} = 'progress';
        

        require_once 'Writer.php';

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