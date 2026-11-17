# Git and GitHub Workflow

## Branching Strategy
We used a feature-branch workflow:
- `main` — stable, demo-ready code only
- `feature/database-queue-status` — Kim
- `feature/interface-docs-testing` — Cesca
- `feature/weather-api-security` — Member 3
- `feature/queue-logic-integration` — Member 4

Every feature started on its own branch, was pushed to GitHub, reviewed via a Pull Request, then merged into `main`.

## Commit Convention
`<area>: <short imperative description>`

Examples used in this project:
- `api: add call-next action with transaction safety`
- `ui: add responsive layout for dashboard table`
- `docs: add installation instructions to README`
- `db: create clinic_queue schema and patients table`
- `weather: integrate Open-Meteo with timeout handling`

## Pull Request Process
1. Create a branch from `main`
2. Commit work in small, focused chunks
3. Push the branch and open a PR
4. Request review from one teammate
5. Address feedback, then squash-merge to `main`
6. Delete the branch after merge

## Issue Tracking
Every feature started as a GitHub Issue with:
- A clear title
- Assigned member
- Acceptance criteria
- Linked PR (via `Closes #N`)

## Evidence
See:
- `evidence/` folder — API and testing screenshots
- GitHub repository Insights page — contributors and commit history
- GitHub Issues and Pull Requests tabs — issue and PR tracking
- GitHub Releases page — `v1.0.0` tag

## Branch: feature/database-queue-status
_Owner: Kim — database and queue-status data._