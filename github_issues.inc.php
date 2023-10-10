<?php

function plugin_github_issues_convert() {
    $num  = func_num_args();
    if ( $num < 3 ) {
        return "Usage: #github_issues([PAT],[owner],[repo],[state],[limit = 5])";
    }

    $args = func_get_args();
    $pat = $args[0];
    $owner = $args[1];
    $repo = $args[2];
    $state = $args[3];
    $limit = $args[4];
    if ($limit == '') {
      $limit = 5;
    }

    $js_code = <<<EOC
import domify from 'https://cdn.pika.dev/domify@1.4.1';
import dayjs from 'https://cdn.pika.dev/dayjs@1.11.6';

document.addEventListener('DOMContentLoaded', async () => {
  const issues = (await getIssues('$owner', '$repo', '$state')).slice(0, $limit);
  const issuesTableDOM = makeIssuesTableDOM(issues);
  document.querySelector('#github-issues-placeholder-$owner-$repo').appendChild(domify(issuesTableDOM));
  document.querySelector('#github-issues-loading-$owner-$repo').style = 'display: none;';
});

async function getIssues(owner, repo, state) {
  const url = `https://api.github.com/repos/\${owner}/\${repo}/issues?sort=updated&state=\${state}`;
  const options = { headers: { Authorization: `Bearer $pat` }};
  const res = await fetch(url, options);
  const issues = await res.json();
  return issues;
}

function makeIssuesTableDOM(issues) {
  const issueRowDOMs = issues.map(buildIssueRowHTML).join('\\n');
  const issuesTableDOM = `
<style>
  .github-issues table {
    border-collapse: collapse;
    border-spacing: 0;
    margin: 0.8em;
  }
  .github-issues td, th {
    border: 1px solid gray;
    padding: 0.4em;
  }
  .github-issues th {
    background-color: #eef5ff;
  }
  .github-issues td, .github-issues th {
    font-size: 1em;
  }
  .github-issues a.icon:before {
    display: none;
  }
  .github-issues .issue-state-open {
    background: #bbdefb;
  }
  .github-issues .issue-state-closed {
    background: #ffcdd2;
;
  }
</style>
<div class="github-issues">
  <table>
    <thead>
      <th>作成日</th>
      <th>更新日</th>
      <th>状態</th>
      <th>タイトル</th>
      <th>タグ</th>
      <th>作成者</th>
      <th>担当者</th>
    </thead>
    <tbody>
    \${issueRowDOMs}
    </tbody>
  </table>
</div>
`;
  return issuesTableDOM;
}

function buildIssueRowHTML(issue) {
  const authorHTML = `
<a class="icon" href="\${issue.user.html_url}">
  <img src="\${issue.user.avatar_url}" title="\${issue.user.login} (@\${issue.user.login})" width="16" alt="\${issue.user.login} (@\${issue.user.login})">
  @\${issue.user.login}
</a>`;

  const assigneeHTML = issue.assignee == null ? '-' : `
<a class="icon" href="\${issue.assignee.html_url}">
  <img src="\${issue.assignee.avatar_url}" title="\${issue.assignee.login} (@\${issue.assignee.login})" width="16" alt="\${issue.user.login} (@\${issue.user.login})">
  @\${issue.assignee.login}
</a>`;
  const labelHTML = issue.labels.map( l => {
    return l.name
  }).join('');

  return `
<tr>
  <td>\${formatDateTime(issue.created_at)}</td>
  <td>\${formatDateTime(issue.updated_at)}</td>
  <td class="issue-state-\${issue.state}">\${issue.state}</td>
  <td><a href="\${issue.html_url}">\${issue.title}</a></td>
  <td>\${labelHTML}</td>
  <td>\${authorHTML}</td>
  <td>\${assigneeHTML}</td>
</tr>`;
}

function formatDateTime(datetime) {
  return dayjs(datetime).format('YYYY-MM-DD(ddd)');
}
EOC;

    return '<div id="github-issues-placeholder-' . $owner . '-' . $repo . '"></div>' .
    '<div id="github-issues-loading-' . $owner . '-' . $repo . '">&#9203; Loading GitHub issues...</div>' .
    '<script type="module">' . $js_code . '</script>';
}
?>
