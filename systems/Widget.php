<?php namespace systems;

class Widget
{
    public static function load($_widgetName, $_data = array())
    {
        $_widgetName = 'app\\http\\widgets\\' . $_widgetName;
        return forward_static_call_array(array($_widgetName, 'run'), $_data);
    }
}
