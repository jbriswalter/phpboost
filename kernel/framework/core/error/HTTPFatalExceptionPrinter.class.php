<?php
/*##################################################
 *                    HTTPFatalExceptionPrinter.class.php
 *                            -------------------
 *   begin                : October 18, 2010
 *   copyright            : (C) 2010 Loic Rouchon
 *   email                : horn@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

class HTTPFatalExceptionPrinter
{
	private $type;
	private $message;
	private $exception;
	private $is_row_odd = true;
	private $output = '';

	public function __construct(Exception $exception)
	{
		$this->exception = $exception;
		$this->type = get_class($this->exception);
		$this->message = str_replace("\n", "<br />", $this->exception->getMessage());
	}

	public function render()
	{
		$this->output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
	<head>
		<title>' . $this->type . ' caught</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="Content-Language" content="en" />
		<style type="text/css">
			body {background-color:#dddddd;}
			table {width:100%;}
			caption {text-align:left;font-size:26px;font-weight:bold;background-color:#536F8B;padding:5px;border-bottom:1px #aaaaaa solid;}
			th {font-size:18px;background-color:#7A99B1;height:30px;}
			tr.section {}
			tr.oddRow {background-color:#ECEEEF;}
			tr.evenRow {background-color:#D2E3F1;}
			td.parameterName {font-size:14px;font-weight:bold;padding:0 10px;}
			td.parameterValue {font-size:14px;}
			h1 {background-color:#536F8B;border:1px #aaaaaa solid;padding:10px;margin:0px;}
			div#exceptionContext .message {font-weight:bold;background-color:#eeeeee;border:1px #aaaaaa solid;padding:10px;}
			table.stack td.prototype {font-weight:bold;padding-right:10px;}
			table.stack td.file {font-size:14px;font-style:italic;}
			table.stack td.line {text-align:right;font-size:12px;width:30px;}
			table.stack td.args {font-size:14px;padding-right:10px;}
			table.stack td.argsDetails {border-top:1px #aaaaaa solid;}
			div#exceptionContext {background-color:#eeeeee;border:1px #aaaaaa solid;padding:10px;}
			div#whyISeeThisPage {background-color:#eeeeee;border:1px #aaaaaa solid;padding:10px;}
			div#httpContext {background-color:#eeeeee;border:1px #aaaaaa solid;padding:10px;}
		</style>
		<script type="text/javascript">
		<!--
		function toggleDisplay(link, eltId) {
			var elt = document.getElementById(eltId);
			var mode = elt.style.display;
			if (mode != \'none\') {
				elt.style.display = \'none\';
				link.innerHTML = \'+\';
			} else {
				elt.style.display = \'table-row\';
				link.innerHTML = \'-\';
			}
		}
		-->
		</script>
	</head>
	<body>
		<div id="exceptionContext">
			<h1>' . $this->type . '</h1>
			<div class="message">' . $this->message. '</div>
			<table cellpadding="2" cellspacing="0" class="stack">
				<caption>STACKTRACE</caption>
				<tr><th></th><th>METHOD</th><th>FILE</th><th>LINE</th></tr>' . $this->build_stack_trace() . '
			</table>
		</div>
		<div id="whyISeeThisPage">
			You see this page because your site is configured to use the <em>DEBUG</em> mode.<br />
			If you want to see the related user error page, you have to disable the <em>DEBUG</em> mode
			from the <a href="' . TPL_PATH_TO_ROOT . '/admin/admin_config.php?adv=1">administration panel</a>.
		</div>
		<div id="httpContext">
			<table cellspacing="0" cellpadding="3 5px"><caption>HTTP Request</caption>
			' . $this->get_http_context() . '
			</table>
		</div>
	</body>
</html>';
		return $this->output;
	}

	private function build_stack_trace()
	{
		$i = 0;
		$this->is_row_odd = true;
		$stack = '';
		foreach ($this->exception->getTrace() as $call)
		{
			$row_class = $this->is_row_odd ? 'oddRow' : 'evenRow';
			$has_args = ExceptionUtils::has_args($call);
			$id = 'call' . $i . 'Arguments';
			$stack .= '<tr class="' . $row_class . '">';
			$stack .= '<td class="args">';
			if ($has_args)
			{
				$stack .= '<a href="#" onclick="toggleDisplay(this, \'' . $id . '\');">+</a>';
			}
			$stack .= '</td>';
			$stack .= '<td class="prototype">' . ExceptionUtils::get_method_prototype($call) . '</td>';
			$stack .= '<td class="file">' . ExceptionUtils::get_file($call) . '</td>';
			$stack .= '<td class="line">' . ExceptionUtils::get_line($call) . '</td>';
			$stack .= '</tr>';
			if ($has_args)
			{
				$stack .= '<tr id="' . $id . '" style="display:none;" class="' . $row_class . '">
				<td colspan="4" class="argsDetails">' . ExceptionUtils::get_args($call) . '</td></tr>';
			}
			$i++;
			$this->is_row_odd = !$this->is_row_odd;
		}
		return $stack;
	}

	private function get_http_context()
	{
		$http_context = '';
		$http_context .= $this->dump_var('GET', $_GET);
		$http_context .= $this->dump_var('POST', $_POST);
		$http_context .= $this->dump_var('COOKIE', $_COOKIE);
		$http_context .= $this->dump_var('SERVER', $_SERVER);
		return $http_context;
	}

	private function dump_var($title, $parameters)
	{
		$dump =  '';
		if (!empty($parameters))
		{
			$this->is_row_odd = true;
			$dump .= '<tr class="section"><th colspan="2" style="text-align:left;padding:0 10px;">' . $title . '</th></tr>';
			foreach ($parameters as $key => $value)
			{
				$dump .= $this->add_parameter($key, $value);
			}
		}
		return $dump;
	}

	private function add_parameter($key, $value)
	{
		$row_class = $this->is_row_odd ? 'oddRow' : 'evenRow';
		$this->is_row_odd = !$this->is_row_odd;
		return '<tr class="' . $row_class. '">' .
			'<td class="parameterName">' . $key . '</td>' .
			'<td class="parameterValue">' . nl2br(htmlspecialchars($value), true) . '</td>' .
		'</tr>' ;
	}
}

?>