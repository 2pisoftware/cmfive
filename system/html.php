    <?php

require_once "classes/html/a.php";
require_once "classes/html/button.php";
require_once "classes/html/form.php";

class Html {

    /**
     * Creates an html table from an array like
     * (
     *   ("one","two","three"),
     * 	 ("hello","world","bla")
     * )
     *
     * @param array $array is the array of data
     * @param string $id is a css id
     * @param string $class is a css class
     * @param boolean $header use first row as <th> if true
     *
     */
    public static function table($data, $id = null, $class = "tablesorter", $header = null) {
        if (empty($data)) return;
        $buffer = "";

        // Opening tags
        $buffer .= "<table class='{$class}'>";
            if (!empty($header)) {
                $buffer .= "<thead><tr>";
                if (is_array($header)) {
                    foreach($header as $h) {
                        if (!is_array($h)) {
                            $buffer .= "<th>{$h}</th>";
                        } else {
                            $buffer .= "<th " . ($h[1] === true ? "class='show-for-medium-up'" : "") . ">{$h[0]}</th>";
                        }
                    }
                } else {
                    // Backwards capability!
                    foreach($data[0] as $h) {
                        $buffer .= "<th>{$h}</th>";
                    }
                    array_shift($data);
                } 
                $buffer .= "</tr></thead>";
            }
            
            $buffer .= "<tbody>";
            foreach($data as $row) {
                $buffer .= "<tr>";
                foreach($row as $column) {
                    if (!is_array($column)) {
                        $buffer .= "<td>{$column}</td>";
                    } else {
                        $buffer .= "<td " . ($column[1] === true ? "class='show-for-medium-up'" : "") . ">{$column[0]}</td>";
                    }
                }
                $buffer .= "</tr>";
            } 
        $buffer .= "</tbody></table>";
        return $buffer;
    }
    
//    static function table($array, $id = null, $class = null, $header = null) {
//        if (!$array || sizeof($array) < 1)
//            return "";
//
//        $jstable = "table" . ($class ? "." . $class : "");
//        $id = $id ? ' id="' . $id . '"' : null;
//        $class = $class ? ' class="' . $class . '"' : null;
//
//        $buf = "<table border='0' " . $id . $class . ">\n";
//        $firstline = true;
//        foreach ($array as $line) {
//            // check if this is header line
//            $ct = "td";
//            if ($firstline) {
//                foreach ($line as $cell) {
//                    $buf.="<colgroup></colgroup>";
//                }
//                $buf.="<thead>\n";
//                $ct = $header ? "th nowrap='true' " : $ct;
//            }
//            $buf.="<tr>\n";
//            foreach ($line as $cell) {
//                $buf.="<$ct>$cell</$ct>";
//            }
//            $buf.="\n</tr>\n";
//            if ($firstline) {
//                $buf.="</thead>\n<tbody>\n";
//                $firstline = false;
//            }
//        }
//        $buf .= "</tbody>\n</table>\n";
//        return $buf;
//    }

    /**
     * Html function to draw a chart, see: http://www.chartjs.org/docs/ for how
     * the data structure and options should be put together for each
     * 
     * @param string $id
     * @param string $type
     * @param array $data
     * @param array $options
     * @param mixed $height
     * @param mixed $width
     * @return string
     */
    public static function chart($id = "chartjs", $type = "line", $data = array(), $options = array(), $height = null, $width = null) {
        // Set default values
        if (empty($height)) {
            $height = "300px";
        }
        if (empty($width)) {
            $width = "400px";
        }
        // Create the canvas
        $buffer = "<canvas id='{$id}' width='{$width}' height='{$height}'></canvas>\n";
        $buffer .= "<script type='text/javascript'>\n";
        // Get canvas context via jQuery
        $buffer .= "\tvar ctx = jQuery(\"#{$id}\").get(0).getContext(\"2d\");\n";

        // Create the chart
        $buffer .= "var chart{$id} = new Chart(ctx).";
        switch (strtolower($type)) {
            case "line":
                $buffer .= "Line";
                break;
            case "bar":
                $buffer .= "Bar";
                break;
            case "radar":
                $buffer .= "Radar";
                break;
            case "polar":
                $buffer .= "PolarArea";
                break;
            case "pie":
                $buffer .= "Pie";
                break;
            case "doughtnut":
                $buffer .= "Doughtnut";
                break;
            default:
                $buffer .= "Line";
        }
        $buffer .= "(" . json_encode($data) . ", " . json_encode($options) . ");";
        $buffer .= "</script>";
        return $buffer;
    }

    /**
     * creates a html link
     * Side note: $alt is an "illegal" parameter for a links
     */
    public static function a($href, $title, $alt = null, $class = null, $confirm = null, $target = null) {
        $a = new \Html\a();
        $a->href($href)->text($title)->setClass($class)->confirm($confirm)->target($target);
        return $a->__toString();
    }

    public static function b($href, $title, $confirm = null, $id = null, $newtab = false, $class = null) {
        $button = new \Html\button();
        $button->href($href)->text($title)->confirm($confirm)->id($id)->setClass($class)->newtab($newtab);
        return $button->__toString();
    }

    /**
     * Creates a link (or button) which will pop up a colorbox
     * containing the contents of the url
     *
     * @param <type> $href   (M) the url to display in the colorbox
     * @param <type> $title  (M) the link title
     * @param <type> $button (O) if true create a buttin instead of a link
     * @param <type> $iframe (O) whether to use an iframe to display the html contents (default: false)
     */
    public static function box($href, $title, $button = false, $iframe = false, $width = null, $height = null, $param = "isbox", $id = null, $class = null, $confirm = null) {
        $onclick = Html::boxOnClick($href, $iframe, $width, $height, $param, $confirm);
        $element = null;
        if ($button) {
            // $tag = "button";
            $element = new \Html\button();
            $element->id($id)->setClass($class)->onclick($onclick)->text($title);
        } else {
            $element = new \Html\a();
            $element->id($id)->setClass($class)->onclick($onclick)->text($title);
        }
        return $element->__toString();
//        return "<" . $tag . (!empty($id) ? " id=$id " : "") . (!empty($class) ? " class=$class " : "") . ($tag == 'a' ? ' href="#" ' : '') . $onclick . "><span>" . $title . "</span></" . $tag . ">";
    }

    public static function boxOnClick($href, $iframe = false, $width = null, $height = null, $param = "isbox", $confirm = null) {
        if ($iframe) {
            $width = ", innerWidth:" . $width;
            $height = ", innerHeight:" . $height;
        }
        // add parameter to indicate that this request is shown inside a box
        $prefix = stripos($href, "?") ? "&" : "?";
        $href .= $prefix . $param . "=1";
        $iframe = $iframe ? "true" : "false";

        $confirm_str = '';
        if ($confirm) {
            $confirm_str = "if(confirm('" . $confirm . "')) { ";
        }
        
        return " onclick=\"{$confirm_str}\$('#cmfive-modal').foundation('reveal', 'open', '{$href}');return false;" . (!empty($confirm) ? "}" : "") . "\" ";
        
//        $parameters = "transition: 'elastic', href:'{$href}', iframe: {$iframe}";
//        if (!empty($width)) {
//            $parameters .= ", " . ($iframe ? "innerWidth:" : "") . "'{$width}'";
//        } else {
//            $parameters .= "," . ($iframe ? "innerWidth:" : "") . "'800px'";
//        }
//        $parameters .= ", " . ($iframe ? "innerHeight:" : "") . "'{$height}'";

//        return " onclick=\"{$confirm_str}\$.colorbox({onComplete:function(){\$(this).colorbox.resize()}, {$parameters}});return false;" . (!empty($confirm) ? "}" : "") . "\" ";
        
//        return " onclick=\"{$confirm_str}\$.colorbox({onComplete:function(){\$(this).colorbox.resize()}, transition:'elastic', href:'" . $href . "', iframe:" . $iframe . $width . $height . "});return false;" . (!empty($confirm) ? "}" : "") . "\" ";
    }

    /**
     * creates a ul from an array structure:
     * ("1","2", array("2.1","2.2"),"3")
     */
    public static function ul($array, $id = null, $class = null, $subclass = null, $type = "ul") {
        if (!$array || sizeof($array) < 1)
            return "";

        $id = $id ? ' id="' . $id . '"' : null;
        $class = $class ? ' class="' . $class . '"' : null;
        $buf = "<{$type}" . $id . $class . ">\n";
        for ($i = 0; $i < sizeof($array); $i++) {
            $cur = $array[$i];
            $next = $i < sizeof($array) - 1 ? $array[$i + 1] : null;
            $buf .= "<li>" . $cur;
            if (is_array($next)) {
                $buf.= $this->ul($next, null, $subclass);
            }
            $buf .="</li>\n";
        }
        $buf .= "</{$type}>\n";
        return $buf;
    }

    /**
     * creates a ol from an array structure:
     * ("1","2", array("2.1","2.2"),"3")
     */
    public static function ol(& $array, $id = null, $class = null, $subclass = null) {
        return ul($array, $id, $class, $subclass, "ol");
    }

    /**
     * creates a simple one column form from the following array:
     * array(
     * 		array("title","type","fieldname","value",{size | array(select options) | cols, rows}),
     *  	...
     * )
     *
     * valid field types are:
     *  text, password, autocomplete, static, date, textarea, section,
     *  select, multiselect, checkbox, hidden
     *  
     * Field type auto uses ui hints from a DbObject.
     *
     * when prefixing a fieldname with a minus sign '-' this field will be read-only
     */
    public static function form($data, $action = null, $method = "POST", $submitTitle = "Save", $id = null, $class = null, $target = "_self", $enctype = null) {
        if (!$data)
            return;
        $hidden = "";
        $id = $id ? ' id="' . $id . '"' : null;
        $class = $class ? ' class="' . $class . '"' : null;
        $enctype = $enctype ? " enctype='" . $enctype . "' " : "";
        $buf = '<form' . $id . $class . $enctype . ' action="' . $action . '" method="' . $method . '" target="' . $target . '">' . "<table width='100%' cellspacing=\"0\" class='form'>\n";
        $valign = ' valign="top" ';

        // Add CSRF Token
        $buf .= "<input type='hidden' name='" . CSRF::getTokenID() . "' value='" . CSRF::getTokenValue() . "' />";

        foreach ($data as $row) {
            $title = !empty($row[0]) ? $row[0] : null;
            $type = !empty($row[1]) ? $row[1] : null;
            $name = !empty($row[2]) ? $row[2] : null;
            $value = !empty($row[3]) ? $row[3] : null;
            $readonly = "";
            // handle disabled fields
            if ($name[0] == '-') {
                $name = substr($name, 1);
                $readonly = " readonly='true' ";
            }
            // span entry fields that have no title
            if (!$title) {
                $colspan = 2;
            } else {
                $colspan = 1;
            }
            if ($type == "text" || $type == "password") {
                $size = !empty($row[4]) ? $row[4] : null;
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                $buf .= '<input' . $readonly . ' style="width:100%;"  type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" size="' . $size . '" id="' . $name . '"/>';
                $buf .= "</td></tr>\n";
            } else if ($type == "autocomplete") {
                $options = !empty($row[4]) ? $row[4] : null;
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                $buf.= Html::autocomplete($name, $options, $value, null, "width: 100%;");
                $buf .= "</td></tr>\n";
            } else if ($type == "date") {
                $size = !empty($row[4]) ? $row[4] : null;
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                $buf .= Html::datePicker($name, $value, $size);
                $buf .= "</td></tr>\n";
            } else if ($type == "datetime") {
                $size = !empty($row[4]) ? $row[4] : null;
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                $buf .= Html::datetimePicker($name, $value, $size);
                $buf .= "</td></tr>\n";
            } else if ($type == "time") {
                $size = !empty($row[4]) ? $row[4] : null;
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                $buf .= Html::timePicker($name, $value, $size);
                $buf .= "</td></tr>\n";
            } else if ($type == "static") {
                $size = !empty($row[4]) ? $row[4] : null;
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                $buf .= $value;
                $buf .= "</td></tr>\n";
            } else if ($type == "textarea") {
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                $cols = !empty($row[4]) ? $row[4] : null;
                $rows = !empty($row[5]) ? $row[5] : null;
                $useCKEditor = true;
                if (isset($row[6])) {
                    $useCKEditor = (boolean) $row[6];
                }
                $buf .= '<textarea name="' . $name . '" rows="' . $rows . '" cols="' . $cols . '" ' . ($useCKEditor ? 'class="ckeditor" ' : '') . ' id="' . $name . '">' . $value . '</textarea>';
                if ($useCKEditor) {
                    $buf .= "<script>CKEDITOR.replace('$name');</script>";
                }
                $buf .= "</td></tr>\n";
            } else if ($type == "section") {
                $buf .= '<tr><td colspan="2" class="section" >' . htmlentities($title);
                $buf .= "</td></tr>\n";
            } else if ($type == "select") {
                $items = !empty($row[4]) ? $row[4] : null;
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                if ($readonly == "") {
                    $buf.= Html::select($name, $items, $value);
                } else {
                    $buf.=$value;
                }
                $buf .= "</td></tr>\n";
            } else if ($type == "multiSelect") {
                $items = !empty($field[4]) ? $field[4] : null;
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                if ($readonly == "") {
                    $buf.= Html::multiSelect($name, $items, $value, null, "width: 100%;");
                } else {
                    $buf.=$value;
                }
                $buf .= "</td>\n";
            } else if ($type == "checkbox") {
                $checked = $value == "1" ? 'checked = "checked"' : "";
                $buf.= "<tr><td  $valign class='fieldtitle'>" . htmlentities($title) . "</td><td $valign >";
                $buf.= "<input type=\"checkbox\" name=\"$name\" value=\"1\" $checked id='" . $name . "'>";
                $buf .= "</td></tr>\n";
            } else if ($type == "hidden") {
                $hidden .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '"/>' . "\n";
            } else if ($type == "file") {
                $size = !empty($row[4]) ? $row[4] : null;
                if ($title) {
                    $buf .= "<tr><td $valign class='fieldtitle'>" . htmlentities($title) . "</td>";
                }
                $buf .= "<td colspan=\"$colspan\">";
                $buf .= '<input style="width:100%;"  type="' . $type . '" name="' . $name . '" size="' . $size . '" id="' . $name . '"/>';
                $buf .= "</td></tr>\n";
            }
        }
        if ($action) {
            $buf .= '<tr><td colspan="2" align="right"><input type="submit" value="' . $submitTitle . '"/></td></tr>';
        }
        $buf .= "</table>\n";
        $buf .= $hidden . "</form>\n";
        return $buf;
    }

    public static function datePicker($name, $value = null, $size = null, $required = null) {
        $buf = '<input class="date_picker" type="text" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '" ' . $required . ' />';
        $buf.= "<script>$('#$name').datepicker({dateFormat: 'dd/mm/yy'});$('#$name').keyup( function(event) { $(this).val('');}); </script>";
        return $buf;
    }

    public static function datetimePicker($name, $value = null, $size = null, $required = null) {
        $buf = '<input class="date_picker" type="text" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '" ' . $required . ' />';
        $buf.= "<script>$('#$name').datetimepicker({ampm: true, dateFormat: 'dd/mm/yy'});$('#$name').keyup( function(event) { $(this).val('');}); </script>";
        return $buf;
    }

    public static function timePicker($name, $value = null, $size = null, $required = null) {
        $buf = '<input class="date_picker" type="text" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '" ' . $required . ' />';
        $buf.= "<script>$('#$name').timepicker({ampm: true, dateFormat: 'dd/mm/yy'});$('#$name').keyup( function(event) { $(this).val('');}); </script>";
        return $buf;
    }

    /**
     * This function invokes multiColForm with default parameters
     * to remove unnecessary html when displaying data
     * 
     * @param Array $data
     * @return String html
     */
    public static function multiColTable($data) {
//      public static function multiColForm($data, $action = null, $method = "POST", $submitTitle = "Save", $id = null, $class = null, $extrabuttons = null, $target = "_self", $includeFormTag = true, $validation = null) {
     
        if (empty($data)) return;
        
        $buffer = "";
//        $form = new \Html\form();
//        
//        // If form tag is needed print it
//        if ($includeFormTag) {
//            $class .= " small-12 medium-8 columns";
//            $form->id($id)->setClass($class)->method($method)->action($action)->target($target);
//                
//            if (in_multiarray("file", $data)) {
//                $form->enctype("multipart/form-data");
//            }
//            
//            $buffer .= $form->open();
//        }
        
        // Set up shell layout
        $buffer .= "<div class='row-fluid small-12 multicolform'>";
        
        // Print internals
        foreach ($data as $section => $rows) {
            
            // Print section header
            $buffer .= "<div class='panel'>";
            $buffer .= "<div class='row-fluid section-header'><h4>{$section}</h4></div>";
            
            // Loop through each row
            foreach ($rows as $row) {
                
                // Print each field
                $fieldCount = count($row);
                $buffer .= "<ul class='small-block-grid-1 medium-block-grid-{$fieldCount} section-body'>";
                
                foreach($row as $field) {
                    
                    $title = !empty($field[0]) ? $field[0] : null;
                    $type = !empty($field[1]) ? $field[1] : null;
                    $name = !empty($field[2]) ? $field[2] : null;
                    $value = !empty($field[3]) ? $field[3] : null;

                    // Can I do this?
                    if (empty($title) and empty($value)) continue;
                                        
                    // Exploit HTML5s inbuilt form validation
                    $required = null;
                    if (!empty($validation[$name])) {
                        if (in_array("required", $validation[$name])) {
                            $required = "required";
                        }
                    }

                    $buffer .= "<li>";
                    
                    // Add title field
                    if (!empty($title)) {
                        $buffer .= "<b class='small-6 columns'>{$title}</b>";
                    }
                    
                    $buffer .= "<div class='small-6 columns'>" . $value . "</div></li>";
                }
                
                $buffer .= "</ul>";
            }
            $buffer .= "</div>";
        }

        // Finish shell div tag
        $buffer .= "</div>";        
        return $buffer;
    }

    /**
     * Creates a complex form where each section can have
     * a different number of columns.
     * 
     * extrabuttons = array("id"=>"title", ..)
     *
     * valid field types are:
     *  text, password, autocomplete, static, date, textarea, section,
     *  select, multiselect, checkbox, hidden
     *
     * when prefixing a fieldname with a minus sign '-' this field will be read-only
     *
     * @param <type> $data
     * @param <type> $action
     * @param <type> $method
     * @param <type> $submitTitle
     * @param <type> $id
     * @param <type> $class
     * @param <type> $extrabuttons
     * @return <type>
     */
    public static function multiColForm($data, $action = null, $method = "POST", $submitTitle = "Save", $id = null, $class = null, $extrabuttons = null, $target = "_self", $includeFormTag = true, $validation = null) {
     
        if (empty($data)) return;
        
        $buffer = "";
        $form = new \Html\form();
        
        // If form tag is needed print it
        if ($includeFormTag) {
            $class .= " small-12 columns";
            $form->id($id)->setClass($class)->method($method)->action($action)->target($target);
                
            if (in_multiarray("file", $data)) {
                $form->enctype("multipart/form-data");
            }
            
            $buffer .= $form->open();
        }
        
        // Set up shell layout
        $buffer .= "<div class='row-fluid small-12 multicolform'>";
        
        // Print internals
        foreach ($data as $section => $rows) {
            
            // Print section header
            $buffer .= "<div class='panel'>";
            $buffer .= "<div class='row-fluid section-header'><h4>{$section}</h4></div>";
            
            // Loop through each row
            foreach ($rows as $row) {
                
                // Print each field
                $fieldCount = count($row);
                $buffer .= "<ul class='small-block-grid-1 medium-block-grid-{$fieldCount} section-body'>";
                
                foreach($row as $field) {
                    
                    $title = !empty($field[0]) ? $field[0] : null;
                    $type = !empty($field[1]) ? $field[1] : null;
                    $name = !empty($field[2]) ? $field[2] : null;
                    $value = !empty($field[3]) ? $field[3] : null;
               
                    // Exploit HTML5s inbuilt form validation
                    $required = null;
                    if (!empty($validation[$name])) {
                        if (in_array("required", $validation[$name])) {
                            $required = "required";
                        }
                    }

                    $readonly = "";
                    
                    $buffer .= "<li>";
                    
                    // Add title field
                    if (!empty($title)) {
                        $buffer .= "<label class='small-12 columns'>$title";
                    }
                    $buffer .= "<div>";
                    
                    // handle disabled fields
                    if ($name[0] == '-') {
                        $name = substr($name, 1);
                        $readonly = " readonly='true' ";
                    }

                    switch($type) {
                        case "text":
                        case "password":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= '<input' . $readonly . ' style="width:100%;" type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" size="' . $size . '" id="' . $name . '" ' . $required . " />";
                        break;
                        case "autocomplete":
                            $options = !empty($field[4]) ? $field[4] : null;
                            $buffer .= Html::autocomplete($name, $options, $value, null, "width: 100%;", 1, $required);
                        break;
                        case "date":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= Html::datePicker($name, $value, $size, $required);
                        break;
                        case "datetime":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= Html::datetimePicker($name, $value, $size, $required);
                        break;
                        case "time":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= Html::timePicker($name, $value, $size, $required);
                        break;
                        case "static":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= $value;
                        break;
                        case "textarea":
                            $c = !empty($field[4]) ? $field[4] : null;
                            $r = !empty($field[5]) ? $field[5] : null;
                            $useCKEditor = true;
                            if (isset($field[6])) {
                                $useCKEditor = $field[6];
                            }
                            $buffer .= '<textarea' . $readonly . ' style="width:100%;" name="' . $name . '" rows="' . $r . '" cols="' . $c . '" ' . ($useCKEditor ? 'class="ckeditor" ' : '') . ' id="' . $name . '" ' . $required . '>' . $value . '</textarea>';
                        break;
                        case "select":
                            $items = !empty($field[4]) ? $field[4] : null;

                            $default = !empty($field[5]) ? ($field[5] == "null" ? null : $field[5]) : "-- Select --";
                            $class = !empty($field[6]) ? $field[6] : null;
                            if ($readonly == "") {
                                $buffer .= Html::select($name, $items, $value, $class, "width: 100%;", $default, $readonly != "", $required);
                            } else {
                                $buffer .= $value;
                            }
                        break;
                        case "multiSelect":
                            $items = !empty($field[4]) ? $field[4] : null;
                            if ($readonly == "") {
                                $buffer .= Html::multiSelect($name, $items, $value, null, "width: 100%;", $required);
                            } else {
                                $buffer .= $value;
                            }
                        break;
                        case "checkbox":
                            $defaultValue = !empty($field[4]) ? $field[4] : null;
                            $class = !empty($field[5]) ? $field[5] : null;
                            $buffer .= Html::checkbox($name, $value, $defaultValue, $class);
                        break;
                        case "radio":
                            $defaultValue = !empty($field[4]) ? $field[4] : null;
                            $class = !empty($field[5]) ? $field[5] : null;
                            $buffer .= Html::radio($name, $group, $value, $defaultValue, $class) . "&nbsp;" . htmlentities($title);
                        break;
                        case "hidden":
                            $buffer .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" id="' . $name . '"/>';
                        break;
                        case "file":
                            $size = !empty($row[4]) ? $row[4] : null;
                            $buffer .= '<input style="width:100%;"  type="' . $type . '" name="' . $name . '" size="' . $size . '" id="' . $name . '"/>';
                        break;
                    }
                    $buffer .= "</div></label>";
                }
                
                $buffer .= "</li></ul>";
            }
            $buffer .= "</div>";
        }
        $buffer .= "<script>$(function(){\$('textarea.ckeditor').each(function(){CKEDITOR.replace(this)})});</script>";
       
        // Finish shell div tag
        $buffer .= "</div>";
        
        // Close form tag if needed
        if ($includeFormTag) {
            $buffer .= $form->close();
        }
        
        return $buffer;
    }

    /**
     * Creates a checkbox input element
     *
     * @param <type> $name
     * @param <type> $value
     * @return <type>
     */
    public static function checkbox($name, $value, $default_value = '1', $class = null, $required = null) {
        $default_value = $default_value === null ? '1' : $default_value;
        $checked = ($value == $default_value ? 'checked = "checked"' : "");
        $buf = "<input type=\"checkbox\" name=\"" . $name . "\" value=\"" . $default_value . "\" $checked  id=\"" . $name . "\" class=\"" . $class . "\" " . $required . " />";
        return $buf;
    }

    /**
     * Creates a radiobutton input element
     *
     * 	@param <type> $id
     * @param <type> $name
     * @param <type> $value
     * @return <type>
     */
    public static function radio($name, $group, $value, $default_value = '1', $class = null, $required = null) {
        $default_value = $default_value === null ? '1' : $default_value;
        $checked = $value == $default_value ? 'checked = "checked"' : "";
        $buf = "<input type=\"radio\" name=\"" . $group . "\" value=\"" . $default_value . "\" $checked  id=\"" . $name . "\" class=\"" . $class . "\" " . $required . " />";
        return $buf;
    }

    /**
     * Create just a single select input widget to be used
     * in a custom form.
     *
     * @param <type> $data
     * @param <type> $value
     * @param <type> $class
     */
    public static function select($name, $items, $value = null, $class = null, $style = null, $allmsg = "-- Select --", $required = null) {
        $buf = '<select id="' . $name . '"  name="' . $name . '" class="' . $class . '" style="' . $style . '" ' . $required . '>';
        if ($items) {
            $buf.= $allmsg ? "<option value=''>" . $allmsg . "</option>" : '';
            foreach ($items as $item) {
                if (is_scalar($item)) {
                    $selected = $value == $item ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item) . '"' . $selected . '>' . htmlentities($item) . '</option>';
                } elseif (is_array($item)) {
                    $selected = $value == $item[1] ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item[1]) . '"' . $selected . '>' . htmlentities($item[0]) . '</option>';
                } elseif (is_a($item, "DbObject")) {
                    $selected = $value == $item->id ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item->getSelectOptionValue()) . '"' . $selected . '>' . htmlentities($item->getSelectOptionTitle()) . '</option>';
                }
            }
        }
        $buf.='</select>';
        return $buf;
    }

    /**
     * Create a grouped select input widget to be used
     * in a custom form.
     *
     * @param <type> $name: name of the select box, group name is pre-defined as $name.'_group';
     * @param <type> $items: associative array including group=>groupitems pairs;
     * @param <type> $value: current value of option item;
     * @param <type> $groupvalue: current group value of optgroup item;
     */
    public static function groupSelect($name, $items, $value = null, $groupvalue = null, $class = null, $style = null, $allmsg = "-- Select --") {
        $buf = '<select id="' . $name . '"  name="' . $name . '" class="' . $class . '" style="' . $style . '">';
        if ($items) {
            $buf.= $allmsg ? "<option value=''>" . $allmsg . "</option>" : '';
            foreach ($items as $groupname => $groupitems) {
                $buf.= '<optgroup label="' . $groupname . '">';
                foreach ($groupitems as $item) {
                    if (is_array($item)) {
                        $selected = ($groupvalue == $groupname && $value == $item[1]) ? ' selected = "true" ' : "";
                        $buf .= '<option value="' . htmlspecialchars($item[1]) . '"' . $selected . '>' . htmlentities($item[0]) . '</option>';
                    } elseif (is_a($item, "DbObject")) {
                        $selected = ($groupvalue == $groupname && $value == $item->id) ? ' selected = "true" ' : "";
                        $buf .= '<option value="' . htmlspecialchars($item->getSelectOptionValue()) . '"' . $selected . '>' . htmlentities($item->getSelectOptionTitle()) . '</option>';
                    } elseif (is_scalar($item)) {
                        $selected = ($groupvalue == $groupname && $value == $item) ? ' selected = "true" ' : "";
                        $buf .= '<option value="' . htmlspecialchars($item) . '"' . $selected . '>' . htmlentities($item) . '</option>';
                    }
                }
                $buf.= '</optgroup>';
            }
        }
        $buf.='</select><input type="hidden" value="' . $groupvalue . '" name="' . $name . '_group">';

        $buf .='<script type="text/javascript">$("#' . $name . ' > optgroup").click(function(){$("[name=' . $name . '_group]").attr("value", $(this).attr("label"));});</script>';

        return $buf;
    }

    /**
     * Create a multi select field using jQuery
     * 
     * @param <type> $name
     * @param <type> $items
     * @param <type> $values
     * @param <type> $class
     * @param <type> $style
     * @param <type> $allmsg
     * @return <type>
     */
    public static function multiSelect($name, $items, $values = null, $class = null, $style = null, $allmsg = null) {
        $buf = '<select  multiple="multiple" id="' . $name . '"  name="' . $name . '[]" class="' . $class . '" style="' . $style . '">';
        if ($items) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $selected = $values && in_array($item[1], $values) ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item[1]) . '"' . $selected . '>' . htmlentities($item[0]) . '</option>';
                } elseif (is_a($item, "DbObject")) {
                    $selected = $values && in_multiarray($item->id, $values) ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item->getSelectOptionValue()) . '"' . $selected . '>' . htmlentities($item->getSelectOptionTitle()) . '</option>';
                } elseif (is_scalar($item)) {
                    $selected = $values && in_array($item, $values) ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item) . '"' . $selected . '>' . htmlentities($item) . '</option>';
                }
            }
        }
        $buf.='</select>';
        $webroot = WEBROOT;
        $buf .=<<<EOT
                <script>
                $('#$name').asmSelect({addItemTarget: 'bottom', removeLabel: '<img src="$webroot/img/bin_closed.png" border="0"/>'});
                $('#$name').change(function(e, data) { $.fn.colorbox.resize(); });
                </script>
EOT;
        return $buf;
    }

    /**
     * Create a single select autocomplete widget
     *
     * @param <type> $data
     * @param <type> $value
     * @param <type> $class
     */
    public static function autocomplete($name, $options, $value = null, $class = null, $style = null, $minLength = 1, $required = null) {
        if ($minLength == null) {
            $minLength = 1;
        }
        $acp_value = $value;
        if (is_array($options)) {
            $source = "[";
            foreach ($options as $option) {
                if (is_array($option)) {
                    $source .= '{"id":"' . $option[1] . '","value":"' . $option[0] . '"},';
                    if ($value == $option[1]) {
                        $acp_value = $option[0];
                    }
                } elseif (is_a($option, "DbObject")) {
                    $source .= '{"id":"' . htmlentities($option->getSelectOptionValue()) . '","value":"' . htmlentities($option->getSelectOptionTitle()) . '"},';
                    if ($value == $option->getSelectOptionValue()) {
                        $acp_value = $option->getSelectOptionTitle();
                    }
                } elseif (is_object($option)) {
                    // Ima go ahead and assume that option will have id and value parameters
                    $source .= json_encode($option) . ", ";
                } elseif (is_scalar($option)) {
                    $source .= '{"id":"' . $option . '","value":"' . $option . '"},';
                }
            }
            // Remove trailing comma
            $source = substr($source, 0, -1);
            $source .= "]";
        } else {
            $source = "'" . $options . "'";
        }

        $buf = '<input type="hidden" id="' . $name . '"  name="' . $name . '" value="' . $value . '"/>';
        $buf.='<input type="text" id="acp_' . $name . '"  name="acp_' . $name . '" value="' . $acp_value . '" class="' . $class . '" style="' . $style . '" ' . $required . ' />';
        $buf.="<script type='text/javascript'>";
        $buf.='$(function(){
                    $("#acp_' . $name . '").autocomplete({
                        minLength:' . $minLength . ', 
                        source: ' . $source . ',
                        select: function(event,ui){
                            $("#' . $name . '").val(ui.item.id); //acp_' . $name . '(event,ui);
                            selectAutocompleteCallback(event, ui);
                        }
                    });
                });';
        $buf.="</script>";
        return $buf;
    }

    public static function img($src, $alt = "") {
        $buf = '<img border="0" src="' . $src . '" alt="' . $alt . '"/>';
        return $buf;
    }

    /**
     * validates the request parameters according to
     * the rules passed in $valarray. It must be of the
     * following form:
     *
     * array(
     *   "<param-name>" => array("<regexp>","<error message>"),
     *   "<param-name>" => array("<regexp>","<error message>"),
     *   ...
     * )
     *
     * returns an array which contains all produced error
     * messages
     */
    public static function validate($valarray) {
        if (!$valarray || !sizeof($valarray))
            return null;
        $error = array();
        foreach ($valarray as $param => $rule) {
            $regex = $rule[0];
            $message = $rule[1];
            $val = $_REQUEST[$param];
            if (!preg_match("/" . $regex . "/", $val)) {
                $error[] = $message;
            }
        }
        return $error;
    }

    public static function pagination($currentpage, $numpages, $pagesize, $totalresults, $baseurl, $pageparam = "p", $pagesizeparam = "ps", $totalresultsparam = "tr") {
        // See functions.php for implementation of isNumber
        // Prepare buffer
        $buf = "<ul class='pagination'>";
        if (isNumber($currentpage) and isNumber($numpages) and isNumber($pagesize) and isNumber($totalresults)) {
            // Check that we're within range
            if ($currentpage > 0 and $currentpage <= $numpages and $numpages > 1) {

                // Build pagination links
                for ($page = 1; $page <= $numpages; $page++) {
                    $buf .= "<li>";

                    // Check if the current page
                    if ($currentpage == $page) {
                        $buf .= "<a href='#' class='active'>$page</a>";
                    } else {
                        $buf .= "<a href='{$baseurl}";
                        $buf .= (strpos($baseurl, "?") == 0 ? "?" : "&");
                        $buf .= "{$pageparam}={$page}&{$pagesizeparam}={$pagesize}&{$totalresultsparam}={$totalresults}'>" . $page . "</a>";
                    }
                    $buf .= "</li>";
                }
            }
        }
        $buf .= "</ul>";
        return $buf;
    }

    /**
     *  Filter function returns formatted form for declaring filters. Data is the same
     *  as how Html::form is used. Filter parameters can be retrieved with $w->request
     *  and it may be a good idea to prefix input names with 'filter_' to avoid naming
     *  collisions in requests 
     *
     *  @param String $legend
     *  @param Array $data
     *  @param String $action
     *  @param String $method
     *  @param String $submitTitle
     *  @param String $id
     *  @param String $class
     *
     *  @return String $buf
     */
    public static function filter($legend, $data, $action = null, $method = "POST", $submitTitle = "Filter", $id = null, $class = null, $validation = null) {
        // This will pretty much be a redesigned Html::form layout
        if (empty($data))
            return;

        $form = new \Html\form();
        // If form tag is needed print it
        $form->id($id)->setClass($class)->method($method)->action($action);
                
        $buffer = "";
        $buffer .= $form->open();
        
        // Set up vars
        $hidden = "";
        $buffer .= "<fieldset style=\"padding: 0; padding-top: 10px; padding-left: 10px;\">\n";
        $buffer .= "<legend>" . $legend . "</legend>\n";
        $buffer .= "<div class=\"row-fluid\">\n";

        // Loop through data
        foreach ($data as $row) {

            $buffer .= "<div class=\"small-12 medium-3 left\"><div class=\"row\">";
            
            // Get row parameters
            $title = !empty($row[0]) ? $row[0] : null;
            $type = !empty($row[1]) ? $row[1] : null;
            $name = !empty($row[2]) ? $row[2] : null;
            $value = !empty($row[3]) ? $row[3] : null;
            $readonly = "";

            $required = null;
            if (!empty($validation[$name])) {
                if (in_array("required", $validation[$name])) {
                    $required = "required";
                }
            }

            // handle disabled fields
            if ($name[0] == '-') {
                $name = substr($name, 1);
                $readonly = " readonly='true' ";
            }

            // span entry fields that have no title
            if (!empty($title)) {
                $mediumCols = 9;
                if ($type == "checkbox") {
                    $mediumCols = 6;
                }
                $buffer .= "<div class='small-12 medium-" . (12 - $mediumCols) . " columns'><label class='inline'>{$title}</label></div>";
                $buffer .= "<div class='small-12 medium-{$mediumCols} columns'>";
            } else {
                $buffer .= "<div class='small-12'>";
            }

//            $buffer .= "<div class=\"small-12 medium-10 columns\">";
            $size = !empty($row[4]) ? $row[4] : null;

            // Get the input that we need
            switch ($type) {
                case "text":
                case "password":
                    $buffer .= '<input' . $readonly . ' style="width:100%;"  type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" size="' . (!empty($row[4]) ? $row[4] : null) . '" id="' . $name . '"/>';
                    break;
                case "autocomplete":
                    $buffer .= Html::autocomplete($name, $size, $value, null, "width: 100%;", 1, $required);
                    break;
                case "date":
                    $buffer .= Html::datePicker($name, $value, $size, $required);
                    break;
                case "datetime":
                    $buffer .= Html::datetimePicker($name, $value, $size, $required);
                    break;
                case "time":
                    $buffer .= Html::timePicker($name, $value, $size, $required);
                    break;
                case "static":
                    $buffer .= $value;
                    break;
                case "textarea":
                    // Columns is the size variable
                    $cols = $size;
                    $rows = !empty($row[5]) ? $row[5] : null;
                    $buffer .= '<textarea name="' . $name . '" rows="' . $rows . '" cols="' . $cols . '" id="' . $name . '">' . $value . '</textarea>';
                    break;
                case "section":
                    $buffer .= htmlentities($title);
                    break;
                case "select":
                    $items = $size;
                    $class = !empty($row[5]) ? $row[5] : null;
                    $style = !empty($row[6]) ? $row[6] : null;
                    $allmsg = !empty($row[7]) ? $row[7] : "-- Select --";
                    // $name, $items, $value=null, $class=null, $style=null, $allmsg = "-- Select --", $required = null
                    if ($readonly == "") {
                        $buffer .= Html::select($name, $items, $value, $class, $style, $allmsg);
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "multiSelect":
                    $items = $size;
                    if ($readonly == "") {
                        $buffer .= Html::multiSelect($name, $items, $value, null, "width: 100%;");
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "checkbox":
                    $buffer .= Html::checkbox($name, $value, $value, $class);
                    break;
                case "hidden":
                    $hidden .= "<input type=\"hidden\" name=\"" . $name . "\" value=\"" . htmlspecialchars($value) . "\"/>\n";
                    break;
                case "file":
                    $buffer .= "<input style=\"width:100%;\" type=\"" . $type . "\" name=\"" . $name . "\" size=\"" . $size . "\" id=\"" . $name . "\"/>";
                    break;
            }

            $buffer .= "</div></div></div>";
        }
        $buffer .= "</div>";
        // Filter button (optional... though optional is pointless)
        if (!empty($action)) {
            $button = new \Html\button();
            if ($submitTitle !== NULL) {
                $buffer .= "<div class=\"left\">" . $button->type("submit")->text($submitTitle)->__toString() . "&nbsp</div>";
            }
            $buffer .= "<div class=\"left\">" . $button->text("Reset")->id("filter_reset")->name("reset")->value("reset")->__toString() . "</div>";
        }
        $buffer .= "\n</fieldset>\n";
        $buffer .= $hidden . "</form>\n";

        return $buffer;
    }

    public static function listGrid($data, $buttons = array(), $perRow = 2) {
        if (!is_array($data)) {
            return;
        }
        
        $buffer = "";
        $mediumPerRow = ($perRow > 1 ? $perRow - 1 : 1);
        $buffer .= "<ul class='small-block-grid-1 medium-block-grid-$mediumPerRow large-block-grid-$perRow'>";
        
        // List data items
        foreach($data as $id => $d) {
            $buffer .= "<li class='grid-list-panel'>";
            $buffer .= "<div class='panel clearfix  '><div class='small-12'>";
            
            if (!empty($d)) {
                // Loop through each lione
                foreach($d as $line) {
                    $buffer .= "<div class='row'><div class='small-12'>";
                    if (!empty($line)) {
                        // If data is an array loop through and print
                        if (is_array($line)) {
                            $row_width = floor(12/count($line));
                            foreach ($line as $item) {
                                // Make the last item in a line text align right
                                $buffer .= "<div class='small-12 medium-{$row_width} small-text-left " . ($item === end($line) ? "medium-text-right " : "") . "columns'>{$item}</div>";
                            }
                        } else {
                            $buffer .= "<div class='small-12 columns'>{$line}</div>";
                        }
                    }
                    $buffer .= "</div></div>";
                }
            }
            $buffer .= "</div></div>";
            
            // Add buttons
            if (!empty($buttons[$id])) {
                $buffer .= "<div class='row'>";
                $button_width = floor(12/count($buttons[$id]));
                $last_increment = 12 - (count($buttons[$id]) * $button_width);
                $button_counter = 0;
                
                // The code just above and below will perfectly fit any amount of buttons (up to 12) in a row
                // I.e. if there are 7 buttons, the first two will be one column wide but the last 5 will be 2 columns (cool huh?)
                foreach($buttons[$id] as $b) {
                    $buffer .= "<div class='small-12 left medium-";
                    $buffer .= (++$button_counter > (count($buttons[$id]) - $last_increment) ? ($button_width + 1) : $button_width);
                    $buffer .= ("'>" . $b . "</div>");
                }
                $buffer .= "</div>";
            }
            
            $buffer .= "</li>";
        }
        
        $buffer .= "</ul>";
        
        return $buffer;
    }
    
}
