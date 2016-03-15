<?php namespace systems;

class Widget
{
    public static function load($widgetName, $data = array())
    {
        $widgetName = 'app\\http\\widgets\\' . $widgetName;
        return forward_static_call_array(array($widgetName, 'run'), $data);
    }
}
