<?php
	$repo_dir = "/srv/users/serverpilot/apps/APPNAME/repo";
	$public_dir = "/srv/users/serverpilot/apps/APPNAME/public";
	$secret = "SECRET";

	if ($_GET["secret"] != $secret) {
		header("HTTP/1.0 401 Unauthorized");
		exit();
	}

	exec("cd " . $repo_dir . " && git fetch");
	exec("cd " . $repo_dir . " && GIT_WORK_TREE=" . $public_dir . " git checkout -f");
	$commit_hash = shell_exec("cd " . $repo_dir . " && git rev-parse --short HEAD");
	file_put_contents("deploy.log", "[" . date("Y-m-d H:i:s") . "] INFO Deployed commit: " . $commit_hash, FILE_APPEND);
?>
