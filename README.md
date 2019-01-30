# Git Auto Deploy

Automatically deploy latest git repository branch via PHP

This tutorial is based on a [ServerPilot](https://serverpilot.io/a/d4ddebee3c49) installation on an Ubuntu based VPS provided by [DigitialOcean](https://m.do.co/c/06e11948f7aa). Other configurations will work with minor modification to the below script.

## Configuration

1. Create an app in ServerPilot.

2. SSH into your server using a system user. `serverpilot`

3. `cd` into `~\.ssh`

4. Run `ssh-keygen`. (Don't assign a password) (You only need a single key for your account on the server)

5. Add the key to your GitHub account SSH Keys (Settings > SSH and GPG Keys > New SSH Key).

6. `cd` into your apps root directory. (Not `public`)

7. Run `git clone --mirror git@github.com:USERNAME/REPONAME.git repo`. (Clones your repo into a `repo` folder)

8. Run an initial deploy from CLI

```
cd repo
GIT_WORK_TREE=/srv/users/serverpilot/apps/APPNAME/public git checkout -f master
```

8. Create a PHP file for GitHub to run when the master branch is altered. (~/apps/APPNAME/public/deploy.php)

```
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
```

9. Add the `POST` hook within GitHub.

```
http://DOMAIN.TLD/deploy.php?secret=1234567890
```

10.  Make changes via Git, then commit them to the "master" branch, and push.

11. The files were now deployed to `~/apps/APPNAME/public` as intended.
  * For additional security, change the name of `deploy.php` to something containing random characters, such as `deploy-23d98th98y.php`. If you do that, don't forget to update the URL you configured in GitHub.

For debugging, the script in that blog post creates a log file at `~/apps/APPNAME/public/deploy.log`. You can also check your app's nginx logs if you aren't sure GitHub made the request.
