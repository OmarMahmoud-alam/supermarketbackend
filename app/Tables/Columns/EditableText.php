<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\Column;
use Filament\Columns\Text;

class EditableText extends Column
{
    protected string $view = 'tables.columns.editable-text';
    public function view($row, $column, $value): string
    {
        return parent::view($row, $column, $value) . '<script src="' . asset('js/editable/editable-text.js') . '"></script>';
    }
  /* public function __construct($name)
    {
        parent::__construct($name);

        $this->scripts([
            'editable-text' => asset('path/to/editable-text.js'),
        ]);
    }

    public function view($row, $column, $value): string
    {
        return parent::view($row, $column, $value);
    }*/
}
