<?php

 $o = new Calendar(12,2018);
echo $o->create();
 
 
 class Calendar {

     protected $month;
     protected $days = [];

     function __construct($month, $year) {
         $this->month = DateTime::createFromFormat('Y-m|', $year . '-' . $month);
         $this->init();
     }

     protected function init() {

         //шапка календаря
         $weekDays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
         foreach ($weekDays as $weekDay) {
             $endRow       = ($weekDay === 'Вс');
             $this->days[] = [
                 'type'   => 'weekday',
                 'label'  => $weekDay,
                 'endRow' => $endRow
             ];
         }

         //заполнители до первого дня месяца
         for ($i = 1, $j = $this->month->format('w'); $i < $j; $i++) {
             $this->days[] = [
                 'type' => 'blank'
             ];
         }
         //заполняем днями
         $today = date('Y-m-d');
         $days  = new DatePeriod($this->month, new DateInterval('P1D'), $this->month->format('t') - 1);
         foreach ($days as $day) {
             $isToday      = ($this->month->format('Y-m-d') == $today);
             $endRow       = ($day->format('w') == 6);
             $this->days[] = [
                 'type'   => 'day',
                 'label'  => $day->format('j'),
                 'toDay'  => $isToday,
                 'endRow' => $endRow
             ];
         }

         //заполнители до конца месяца
         if (!$endRow) {
             for ($i = 0, $j = 7 - $day->format('w'); $i < $j; $i++) {
                 $this->days[] = [
                     'type' => 'blank'
                 ];
             }
         }
//         echo '<pre>'; print_r($this->days);
     }

     public function create($options = []) {
         if (!isset($options['id']))
             $options['id'] = 'calendar';
         
         $class = $options['id'];

         $output = '';
         $output .= '<table border="1" id="' . $class . '">';
         $output .= '<tr>';
         $i = 1; $today = '';
         foreach ($this->days as $day) {
             if ($day['today']) $today = 'style="background: yellow;"';
             $output .= "<td $today>{$day['label']}</td>";
             if ($i % 7 === 0)
                 $output .= '</tr><tr>';
             $i++;
         }
         $output .= '</tr>';
         $output .= '</table>';
         return $output;
     }

 }