<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $view->extend('InstallBundle:Install:layout.html.php') ?>
<?php $view['slots']->start('subtitle') ?>Step 3: Check URL rewriting<?php $view['slots']->stop() ?>

<style type="text/css" xmlns="http://www.w3.org/1999/html">
	#url_check_loading article {
		background: url(../../web/images/spinners/loading-big-circle.gif) no-repeat 50% 0;
		padding-top: 36px;
		text-align: center;
	}

	.faux-url {
		font-style: normal;
		padding: 1px 4px;
		font-family: Monaco, Courier, monospace;
		font-size: 11px;
	}
	.faux-url i {
		font-style: normal;
		margin-bottom: 1px;
	}

	i {
		font-style: normal;
	}

	table.layout { border: 0; margin: 0; }
	table.layout td { border: 0; margin: 0; padding: 2px; vertical-align: middle; }

	.codebox {
		font-family: Monaco, Courier, monospace;
		font-size: 11px;
		background-color: #EDEDED;
		border: 1px solid #929292;
		padding: 5px;
		margin: 2px 0 6px;
	}

	.kb-read-more.inline {
		float: none;
		display: inline-block;
		margin-left: 0;
		margin-top: 12px;
	}
</style>
<script type="text/javascript">
$(document).ready(function() {
	var baseurl = window.location.href;
	baseurl = baseurl.replace(/\/index\.php\/(.*?)$/, '');
	$('.dp-url-base').text(baseurl);

	var basepath = window.location.href;
	basepath = basepath.replace(/^(.*?)\/index\.php\/(.*?)$/, '$1');
	basepath = basepath.replace(/https?:\/\//, '');
	if (basepath.indexOf('/') == -1) {
		basepath = '/';
	} else {
		basepath = basepath.replace(/^(.*?)\/(.*?)$/, '/$2');
	}

	$('.dp-base-path').text(basepath);

	$.ajax({
		url: baseurl + '/__checkurlrewrite/path',
		timeout: 8000,
		dataType: 'html',
		complete: function() {
			$('#url_check_loading').hide();
		},
		error: function() {
			$('#url_check_off').show();
		},
		success: function(content) {
			if (content.indexOf('dp_check_okay') !== -1) {
				$('#url_check_pass').show();
			} else {
				$('#url_basepath_wrong').show();
			}
		}
	});
});
</script>

<h3>Checking for clean URL support</h3>
<table class="bordered-table">
	<tbody>
		<tr>
			<td>
				DeskPRO can use clean and short URLs when your server supports it. When clean URLs are enabled, the "index.php" segment
				of the URL is removed. For example:

				<table cellpadding="1" cellspacing="1" class="layout" style="margin-top: 8px">
					<tr>
						<td width="140">With clean URLs:</td>
						<td><em class="faux-url"><i class="dp-url-base"></i>/kb/1-example-article</em></td>
					</tr>
					<tr>
						<td>Without clean URLs:</td>
						<td><em class="faux-url"><i class="dp-url-base"></i>/index.php/kb/1-example-article</em></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr id="url_check_loading">
			<td>
				<article>Performing tests</article>
			</td>
		</tr>
		<tr id="url_check_pass" style="display: none">
			<td>
				<span class="label success" style="float:right">OK</span>
				Your server supports URL rewriting.
			</td>
		</tr>
		<tr id="url_check_off" style="display: none">
			<td>
				<span class="label notice" style="float:right">UNSUPPORTED</span>
				Your server is not capable of URL rewriting.
				<br />
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.url_rewriting') ?>" class="kb-read-more inline" target="_blank">Learn about enabling URL rewriting on your server &rarr;</a>
			</td>
		</tr>
		<tr id="url_basepath_wrong" style="display: none">
			<td>
				<span class="label important" style="float:right">PROBLEM</span>
				We have detected that your server supports URL writing, but it is not configured properly. This can sometimes happen if the document root of your
				site and the directory that DeskPRO is being served from are different (for example, if you use Apache Aliases).
				<br /><br />

				To fix this problem, you need to make a small change to the <em class="faux-url">.htaccess</em> file in the root DeskPRO directory.
				Find the line that looks like this;
				<div class="codebox">#RewriteBase /deskpro</div>
				You need to remove the leading "#" and then change the path to the root URL of DeskPRO:
				<div class="codebox">RewriteBase <i class="dp-base-path"></i></div>

				<br />
				This step is optional. If you continue without correcting the probelm, DeskPRO will still function perfectly without the clean URLs.
				You can re-enable clean URLs at any time from the "Settings" section in the Admin Interface after installation.
				<br />
				<a href="<?php echo \Application\DeskPRO\App::get('deskpro.service_urls')->get('dp.kb.install.url_rewriting') ?>" class="kb-read-more inline" target="_blank">Learn more about URL rewriting &rarr;</a>
			</td>
		</tr>
	</tbody>
</table>

<div class="alert-message block-message success">
	<div class="alert-actions submit-area">
		<a class="btn" href="<?php echo $view['router']->generate('install_verify_files') ?>" onclick="$(this).parent().addClass('clicked');">Go to step 4: Verify file integrity</a>
		<span class="next-loading"></span>
	</div>
</div>