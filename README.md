# pukiwiki-plugin-github-issues

Pukiwiki plugin to show the table listing GitHub issues.

![スクリーンショット](screenshot.png)

## Usage

```
#github_issues([PAT],[owner],[repo],[state],[limit = 5])
```

### Parameters

| Name | Description |
| -- | -- |
| PAT | Personal Access Token to read GitHub issues information |
| owner | GitHub repository owner name (example: `tsukuba-hpcs`) |
| repo | Project ID you want to show issues (example: `pukiwiki-plugin-github-issues`) |
| state | Issue status such as `open` and `closed` |
| limit (optional) | Issue count to show on the table |

## References

- Issues - GitHub Docs - https://docs.github.com/en/free-pro-team@latest/rest/issues/issues?apiVersion=2022-11-28#list-issues-assigned-to-the-authenticated-user

## License

[GNU GPLv3](LICENSE)
