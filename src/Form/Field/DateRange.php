<?php

namespace Encore\Incore\Form\Field;

use Encore\Incore\Form\Field;

class DateRange extends Field
{
    protected static $css = [
        '/packages/docore/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
    ];

    protected static $js = [
        '/packages/docore/moment/min/moment-with-locales.min.js',
        '/packages/docore/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
    ];

    protected $format = 'YYYY-MM-DD';

    /**
     * Column name.
     *
     * @var string
     */
    protected $column = [];

    public function __construct($column, $arguments)
    {
        $this->column['start'] = $column;
        $this->column['end'] = $arguments[0];

        array_shift($arguments);
        $this->label = $this->formatLabel($arguments);
        $this->id = $this->formatId($this->column);

        $this->options(['format' => $this->format]);
    }

    public function prepare($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }

    public function render()
    {
        $this->options['locale'] = config('app.locale');

        $startOptions = json_encode($this->options);
        $endOptions = json_encode($this->options + ['useCurrent' => false]);

        $class = $this->getElementClass();

        $this->script = <<<EOT
            $('.{$class['start']}').datetimepicker($startOptions);
            $('.{$class['end']}').datetimepicker($endOptions);
            $(".{$class['start']}").on("dp.change", function (e) {
                $('.{$class['end']}').data("DateTimePicker").minDate(e.date);
            });
            $(".{$class['end']}").on("dp.change", function (e) {
                $('.{$class['start']}').data("DateTimePicker").maxDate(e.date);
            });
EOT;

        return parent::render();
    }
}
