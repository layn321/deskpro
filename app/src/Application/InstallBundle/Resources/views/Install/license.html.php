<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $view->extend('InstallBundle:Install:layout.html.php') ?>
<?php $view['slots']->start('subtitle') ?>Step 1: License Agreement<?php $view['slots']->stop() ?>
<?php $failed = false ?>
<h3>License Agreement</h3>
<table class="bordered-table zebra-striped">
	<tbody>
		<tr>
			<td>
				<div style="height: 300px; overflow: auto;">
					<pre style="font-family: monospace; border: none; margin: 0; padding: 0;">
This document contains the End User License Agreement between DeskPRO Ltd.
herein referred to as "the company", "we", "us", and "our",
and the End User (herein referred to as "you", "your", "yours", etc.). By
installing and using DeskPRO (herein referred to as "the software"), you are
agreeing to these terms and conditions.

Terms and Definitions

An "instance" of the software is a unique installation of the software on a
system capable of running it, whether used for production purposes or for
testing.
An "agent" is a unique account installed in an instance of
DeskPRO, used by a member of your staff to provide support assistance
to your users.
An "administrator" or "admin" is an agent with additional administration
privileges.
A "user" is a unique account installed in an instance of DeskPRO, used
by one or more persons to obtain support from your staff.
A "ticket" is a single support request tracked in the software. These
are normally submitted by your users but can also be created by your agents.

Right To Run

Your purchase of a DeskPRO license grants you the right to make use of a
single instance of the software. You may temporarily install a second instance
of DeskPRO on an internal development server for testing and deployment
planning, but only one instance of DeskPRO shall be in operation (available
for access to your users or agents).

If you wish to run more than one instance of DeskPRO, you must purchase an
additional license for each additional instance.

Modifications to the software or database to circumvent the
one-license-one-instance rule are prohibited.

Licensee

The Software is licensed only to you. You may not rent, lease, sublicence,
sell, assign, pledge, transfer or otherwise dispose of the Software in any
form, on a temporary or permanent basis, without the prior written consent of
DeskPRO Ltd.

Accurate License Information

Your right to use a DeskPRO instance under a purchased license requires you to
provide accurate information regarding the location (or planned location) of
the software instance. You must maintain accurate contact information in your
Members Area profile and maintain accurate location details for your software
instance(s).

Alteration of Source Code

You are permitted to alter the source code of DeskPRO subject to the following
conditions:

You may not distribute the software or any portion thereof, or permit or cause
to be exposed any portion of the source code to any other party without the
express written consent of DeskPRO Ltd. You may not attempt to
circumvent any license validation checks. Unless you have purchased a
copyright removal license, all copyright notices must remain intact in the
source code.

Removal of Copyright Notices

If you have not purchased a copyright removal license, all copyright notices
included in the software (including in source code and templates) must remain
intact. This includes "Powered by DeskPRO" and similar notices. You may,
however, change the appearance of all other aspects of the interface.

If you have purchased the copyright removal license, you may make any desired
changes to templates, including to the copyright notices.

Circumvention of Software Limits

If you have purchased a version of the software that includes restrictions on
any resource, you may not attempt to circumvent the internal checks performed
by the software to ensure compliance with these restrictions.

Verification of Minimum Server Requirements

If you have purchased a license for a standalone instance of DeskPRO, it is
your responsibility to verify your target equipment meets the minimum
requirements specified by the software's documentation. Refunds are not
offered.

Reporting and Server Statistics

The Software will occasionally send reports to a DeskPRO Ltd. server:

    - Install/Import: During and/or after an install or import, diagnostic information
      pertaining to the install or import procedure will be submitted. If there are errors,
      error information will also be included.

    - Errors: When the server encounters an error, error information will be
      automatically submitted so our engineers can identify and fix problems
      as soon as possible.

    - Heartbeat pings: A "heartbeat" is submitted once a day for statistical
      purposes. This allows us to determine the number of active licenses
      and installations.

Some reports contain additional data, such as server information or database
information, that is used for statistical purposes. This data is kept
private and is never given to third-parties.

<input type="checkbox" style="width: 10px;" name="stats_opt_out" /> If you do not wish to submit additional statistical data, check this box to opt-out.

License Transfer

We may, at our discretion, allow you to transfer your license to another
party, providing the license:

has not already been transferred
was purchased more than 4 months ago the
members area access is active
the license was purchased at full cost with no discount
the price paid is higher than or equal to current retail value

THE SOFTWARE AND THE ACCCOMPANYING FILES ARE SOLD "AS IS" AND WITHOUT
WARRANTIES AS TO PERFORMANCE OF MERCHANTABILITY OR ANY OTHER WARRANTIED
WHETHER EXPRESSED OR IMPLIED.

NO ORAL OR WRITTEN INFORMATION OR ADVICE GIVEN BY DESKPRO LIMITED,
ITS DEALERS, DISTRIBUTORS, AGENTS OR EMPLOYEES SHALL CREATE A WARRANTY OR IN
ANY WAY INCREASE THE SCOPE OF ANY WARRANTY PROVIDED HEREIN.

DESKPRO LIMITED SHALL HAVE NO RESPONSIBILITY IF THE SOFTWARE HAS
BEEN ALTERED IN ANY WAY, OR FOR ANY FAILURE THAT ARISES OUT OF USE OF THE
SOFTWARE WITH OTHER THAN A RECOMMENDED HARDWARE CONFIGURATION, PLATFORM OR
OPERATING SYSTEM.

NEITHER DESKPRO LIMITED NOR ITS SUPPLIERS SHALL BE LIABLE TO YOU
OR ANY THIRD PARTY FOR ANY INDIRECT, SPECIAL, INCIDENTAL, PUNITIVE, COVER OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, DAMAGES FOR THE
INABILITY TO USE EQUIPMENT OR ACCESS DATA, LOSS OF BUSINESS, LOSS OF PROFITS,
BUSINESS INTERRUPTION OR THE LIKE), ARISING OUT OF THE USE OF, OR INABILITY TO
USE, THE SOFTWARE AND BASED ON ANY THEORY OF LIABILITY INCLUDING BREACH OF
CONTRACT, BREACH OF WARRANTY, TORT (INCLUDING NEGLIGENCE), PRODUCT LIABILITY
OR OTHERWISE, EVEN IF DESKPRO LIMITED OR ITS REPRESENTATIVES HAVE
BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES AND EVEN IF A REMEDY SET FORTH
HEREIN IS FOUND TO HAVE FAILED OF ITS ESSENTIAL PURPOSE.

Termination of License

DeskPRO Ltd. reserves the right to terminate your license if
any clause of this agreement is found to have been violated.

Enforcability

This Agreement constitutes the complete statement of the agreement between you
and DeskPRO Ltd, and supercedes all representations,
understandings or prior agreements between you and DeskPRO Ltd.

DeskPRO Ltd reserves the right to modify these terms at any time.

This Agreement is governed by the laws of England and Wales.
					</pre>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div class="alert-message block-message warn" id="agreement_box">
	<label style="float: none; width: 100%;"><input type="checkbox" id="accept_check" tabindex="1" /> I agree to the above license agreement</label>

	<div class="alert-actions submit-area">
		<a class="btn disabled" tabindex="2" id="next_btn" href="<?php echo $view['router']->generate('install_checks') ?>" onclick="if (!$(this).hasClass('disabled')) { $(this).parent().addClass('clicked'); }">Go to step 2: Perform server checks</a>
		<span class="next-loading"></span>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#accept_check').on('click', function() {
		if (this.checked) {
			$('#agreement_box').removeClass('warn').addClass('success');
			$('#next_btn').removeClass('disabled');
		} else {
			$('#agreement_box').removeClass('success').addClass('warn');
			$('#next_btn').addClass('disabled');
		}
	});

	$('#next_btn').on('click', function(ev) {
		if ($(this).hasClass('disabled')) {
			ev.preventDefault();
		}
	});

	$('#stats_expand').click(function() {
		$(this).text('Here is the data that will be submitted:').css({'border-bottom': 'none', 'cursor': 'default'});
		$('#stats_list').show();
	});
});
</script>
