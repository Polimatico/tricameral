# Tricameral

**Tricameral** is an open-source platform for collaborative law-making, applying the principles and workflows of open-source software development to the process of drafting, debating, and refining legislation.

Just as software developers use version control, forks, pull requests, and issue trackers to build software together, Tricameral gives citizens, communities, and organizations the same tools to build laws together — transparently, incrementally, and democratically.

## The Idea

Modern legislation suffers from the same problems that plagued software before open source: opaque authorship, no versioning, no easy way to propose changes, no public audit trail, and no mechanism for community participation.

Open-source software solved these problems with a simple model:

- Anyone can read the code
- Anyone can copy it (fork) and adapt it
- Anyone can propose a change (pull request)
- Maintainers review and merge accepted changes
- Everyone can discuss and open issues

Tricameral applies this model to lawmaking. A **project** is a law. A **fork** is a derivative legal text. A **pull request** is a proposed modification. An **issue** is a reported problem with the current law. An **opinion** is a public position, debated and voted on by the community.

---

## Core Concepts

### Projects (Laws)

Each project represents a law or legal document. It contains three editable documents:

| Document | Purpose |
|----------|---------|
| **README** | Overview, context, and intent of the law |
| **Code of Conduct** | Governance rules for the project itself |
| **Law Text** | The actual legislative text |

Projects can be **public**, **private**, or **restricted** (members-only), with fine-grained permissions for who can fork, submit pull requests, and view proposals.

### Forks (Derivative Laws)

Any user with fork permission can create a complete copy of a project and modify it independently. Forks maintain a reference to their origin, making it easy to track how laws evolve across communities and jurisdictions. Project owners can control fork visibility — listing all forks automatically or curating them manually.

### Pull Requests (Proposed Amendments)

Users work on a fork, then open a pull request to propose merging their changes back into the original law. Each pull request includes a title, a detailed rationale, a comment thread for discussion, and a status (`open`, `accepted`, or `rejected`).

When a pull request is accepted, the project owner reviews a line-by-line diff across all three documents and chooses a merge strategy per file:

- Keep the original version
- Accept the proposed version
- Manually write a combined version

This merge workflow is powered by a custom LCS (Longest Common Subsequence) diff engine that shows exactly what changed, line by line, with context.

### Issues (Problems and Debates)

Issues are used to report problems with a law, propose discussions, or track known gaps. They support comment threads, color-coded tags for categorization, and open/closed status — mirroring the bug-tracker model from software development.

### Opinions (Democratic Discourse)

Opinions are the civic layer of Tricameral. Any user who can view a project can post a public position on it. Others can reply and vote (upvote or downvote). Opinions are ranked by vote score, surfacing the most supported positions to the top.

### Team & Contributors

Each project has a team with three roles: **Viewer**, **Editor**, and **Admin**. Additionally, any user whose pull request has been merged is automatically credited as a contributor — creating a transparent record of who shaped the law.

---

## Workflow Example

```
1. A community creates a project: "City Budget Transparency Act"
2. They draft the README (intent), Code of Conduct (governance), and Law Text
3. They make the project public, allowing anyone to fork and submit pull requests
4. A citizen forks the project and proposes stronger penalty clauses
5. They open a pull request with a rationale and reference to existing case law
6. The community discusses it in comments and opinions
7. Opinions are voted on — the community's position becomes visible
8. The project admin reviews the diff and merges the accepted amendment
9. The law text is updated; the contributor is credited
```

---

## Features

- **Version-controlled law documents** — README, Code of Conduct, and Law Text per project
- **Fork system** — derivative laws with tracked lineage, manual or automatic listing
- **Pull requests** — proposed amendments with discussion threads and status tracking
- **LCS-based diff and merge** — line-by-line diff viewer with per-document merge strategies
- **Issue tracker** — problem reporting with tags, comments, and open/closed status
- **Opinion system** — public positions with threaded replies and democratic voting
- **Role-based access control** — Viewer, Editor, Admin per project
- **Granular permissions** — control who can fork, submit PRs, and view proposals
- **Contributor tracking** — automatic attribution from merged pull requests
- **Multi-language support** — user-selectable locale
- **Stars** — bookmark and follow projects

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.4, Laravel 13 |
| Frontend | Tailwind CSS v4, Vite |
| Rich Text | TipTap editor, Marked.js |
| Testing | Pest v4 |
| Database | MariaDB |

---

## Getting Started

### Prerequisites

- PHP 8.4+
- Composer
- Node.js + npm
- A MariaDB or MySQL database

### Installation

```bash
git clone https://github.com/your-org/tricameral.git
cd tricameral

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure your database credentials in `.env`, then:

```bash
php artisan migrate
npm run build
php artisan serve
```
---

<br>
<br>
Yes, I used Claude Code to build this MVP.