<?php
/**
 * Uecode Library Base Class
 * @author Aaron Scherer
 * @date 2013
 * @copyright
 */

class Uecode
{
	/**
	 * This function is used to dump, in a readable format, a given object or array.
	 *
	 *
	 * @static
	 * @param mixed $var Array or Object to Dump
	 * @param int $depth depth to decent into $var
	 * @param boolean $die Do we die after being called
	 * @param boolean $dumpOut Do we return our output instead of echoing it
	 * @return string
	 */
	public static function dump( $var, $depth = 5, $die = false, $dumpOut = false )
	{
		$echo = '';
		$scope = false;
		$prefix = 'unique';
		$suffix = 'value';

		if ($scope) {
			$vals = $scope;
		}
		else {
			$vals = $GLOBALS;
		}

		$old = $var;
		$var = $new = $prefix . rand() . $suffix;
		$vname = FALSE;
		foreach ($vals as $key => $val)
			if ($val === $new) {
				$vname = $key;
			}
		$var = $old;

		$echo .= "<div class='debugDump'>\n\t\t\t<div class='header'>\n\t\t\t\t<h2>Debug</h2>\n\t\t\t</div>";
		$echo .= "\n\t\t\t<section style='font-size: 11px; line-height: 13px; background-color: white; padding: 10px; overflow-x: auto;'>";
		$echo .= "\n\t\t\t\t<pre style='word-wrap: break-word'>";
		$echo .= "\n\t\t\t\t\t" . self::do_dump($var, '$' . $vname, NULL, NULL, $depth);
		$echo .= "\n\t\t\t\t</pre>\n\t\t\t</section>\n\t\t</div>";

		if($dumpOut === false ) {
			if( $die ) {
				$html = "<!DOCTYPE html>\n<html>\n\t<head>\n\t\t<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />\n\t\t<title>Dump</title>\n\t</head>\n\t<body>";
				$html .= "\n\t\t" . $echo . "\n\t</body>\n</html>";
				die( $html );
			}
			echo $echo;
			return null;
		}
		return $echo;
	}

	/**
	 * This function is called by Uecode::dump() that does all the logic.
	 *
	 * @static
	 * @param mixed $var
	 * @param null $var_name
	 * @param null $indent
	 * @param null $reference
	 * @param int $depth
	 * @return string
	 */
	private static function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL, $depth = 5)
	{
		$echo = "\n";
		$BADCLASSES = array('DateTimeZone', 'DateTime');
		$BADMETHODS = array('get','getEscaper','getFlash','getUriForPath','getMimeType','getFormat', 'getParameter', 'getLocateExpression','getListTableConstraintsSQL','getListTableIndexesSQL');
		if($depth < 0) return;
		$do_dump_indent = "\t<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp; ";
		$reference = $reference . $var_name;
		$keyvar = 'the_do_dump_recursion_protection_scheme';
		$keyname = 'referenced_object_name';

		if (is_array($var) && isset($var[$keyvar])) {
			$real_var = &$var[$keyvar];
			$real_name = &$var[$keyname];
			$type = ucfirst(gettype($real_var));
			$echo .= "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
		}
		else {
			$var = array($keyvar => $var, $keyname => $reference);
			$avar = &$var[$keyvar];

			$type = ucfirst(gettype($avar));
			if ($type == "String") {
				$type_color = "<span style='color:green'>";
			}
			elseif ($type == "Integer") {
				$type_color = "<span style='color:red'>";
			}
			elseif ($type == "Double") {
				$type_color = "<span style='color:#0099c5'>";
				$type = "Float";
			}
			elseif ($type == "Boolean") {
				$type_color = "<span style='color:#92008d'>";
			}
			elseif ($type == "NULL") {
				$type_color = "<span style='color:black'>";
			}

			if (is_array($avar)) {
				$count = count($avar);
				$echo .= "$indent" . ($var_name ? "$var_name => " : "") . "<span style='color:#a2a2a2'>$type ($count)</span><br>$indent(<br>";
				$keys = array_keys($avar);
				foreach ($keys as $name) {
					$value = &$avar[$name];
					$echo .= self::do_dump($value, "['$name']", $indent . $do_dump_indent, $reference, $depth - 1);
				}
				$echo .= "$indent)<br>";
			}
			elseif (is_object($avar)) {
				$class = get_class($avar);
				$echo .= "$indent$var_name <span style='color:#a2a2a2'>$class Object</span><br>$indent(<br>";
				$props = get_class_methods($avar);

				foreach($props as $prop)
				{
					if(
							( strpos($prop, 'get') === 0) &&
							( strpos($prop, 'set') !== 0) &&
							( $prop != 'get' ) &&
							( strpos($prop, '__') !== 0 )
					  )
					{
						$r = new ReflectionMethod($class, $prop);
						$params = $r->getParameters();
						if(!in_array($class, $BADCLASSES) && !in_array($prop, $BADMETHODS) && empty($params))
						{
							try
							{
								$item = $avar->$prop();
								if(strpos($prop, 'get') === 0)
									$name = substr($prop, 3);
								$echo .=self::do_dump($item, "$name", $indent . $do_dump_indent, $reference, $depth - 1);
							}
							catch(Exception $e)
							{
								$echo .= "NoData";
							}
						}
					}
				}
				$vars = get_object_vars($avar);
				foreach($vars as $k => $v)
				{
					if($k != '__isInitialized__')
						$echo .=self::do_dump($v, "$k", $indent . $do_dump_indent, $reference, $depth - 1);
				}

				$echo .= "$indent)<br>";
			}
			elseif (is_int($avar)) {
				$echo .= "$indent$var_name = <span style='color:#a2a2a2'>$type(" . strlen($avar) . ")</span> $type_color$avar</span><br>";
			}
			elseif (is_string($avar)) {
				$echo .= "$indent$var_name = <span style='color:#a2a2a2'>$type(" . strlen($avar) . ")</span> $type_color\"".htmlentities($avar)."\"</span><br>";
			}
			elseif (is_float($avar)) {
				$echo .= "$indent$var_name = <span style='color:#a2a2a2'>$type(" . strlen($avar) . ")</span> $type_color$avar</span><br>";
			}
			elseif (is_bool($avar)) {
				$echo .= "$indent$var_name = <span style='color:#a2a2a2'>$type(" . strlen($avar) . ")</span> $type_color" . ($avar == 1 ? "TRUE" : "FALSE") . "</span><br>";
			}
			elseif (is_null($avar)) {
				$echo .= "$indent$var_name = <span style='color:#a2a2a2'>$type(" . strlen($avar) . ")</span> {$type_color}NULL</span><br>";
			}
			else {
				$echo .= "$indent$var_name = <span style='color:#a2a2a2'>$type(" . strlen($avar) . ")</span> $avar<br>";
			}

			$var = $var[$keyvar];
		}
		return $echo;
	}

	/**
	 * Parse a URL
	 *
	 * @param string $url Url to parse
	 */
	public static function parseUrl($url)
	{
		$r  = "^(?:(?P<scheme>\w+)://)?";
		$r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
		$r .= "(?P<host>(?:(?P<subdomain>[-\w\.]+)\.)?" . "(?P<domain>[-\w]+\.(?P<extension>\w+)))";
		$r .= "(?::(?P<port>\d+))?";
		$r .= "(?P<path>[\w/-]*/(?P<file>[\w-]+(?:\.\w+)?)?)?";
		$r .= "(?:\?(?P<arg>[\w=&]+))?";
		$r .= "(?:#(?P<anchor>\w+))?";
		$r = "!$r!";

		preg_match ( $r, $url, $out );

		$joinedExtensions = array('us.com', 'us.org', 'us.net');
		if(in_array($out['domain'], $joinedExtensions))
		{
			if(strpos($out['subdomain'], '.') !== false)
			{
				$temp = explode('.', $out['subdomain']);
				$out['subdomain'] = $temp[0];
				$out['extension'] = $out['domain'];
				$out['domain'] = $temp[1] . '.' . $out['domain'];
			}
			else
			{
				$out['extension'] = $out['domain'];
				$out['domain'] = $out['subdomain'] . '.' . $out['domain'];
				$out['subdomain'] = '';
			}
		}

		foreach( $out as $key => $value ) {
			if( is_numeric( $key ) ) {
				unset( $out[ $key ] );
			}
		}

		return $out;
	}
}
