<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RemoveButton extends Component
{
    public $url;
    public $name;
    public $type;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($url, $name, $type)
    {
        $this->url = $url;
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.remove-button');
    }
}
