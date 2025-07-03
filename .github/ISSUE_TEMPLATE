name: Bug Report
description: Report a reproducible bug to help us fix it faster.
title: "[BUG] <short description>"
labels: [bug]
body:
  - type: markdown
    attributes:
      value: |
        Ensure you're using the latest version and that this issue hasn't already been reported (open or closed).

  - type: dropdown
    id: issue_check
    attributes:
      label: No relevant issue exists
      description: Have you confirmed this bug is not already reported?
      options:
        - Yes, I have checked
    validations:
      required: true

  - type: textarea
    id: bug_description
    attributes:
      label: Describe the bug
      description: Provide a clear and concise description of the bug.
      placeholder: A short, factual summary of the issue.
    validations:
      required: true

  - type: dropdown
    id: docker_usage
    attributes:
      label: Are you using the Docker image?
      options:
        - Yes
        - No
    validations:
      required: true

  - type: input
    id: version
    attributes:
      label: What version are you running?
      placeholder: e.g., v1.24.3
    validations:
      required: true

  - type: dropdown
    id: db_used
    attributes:
      label: Database used
      options:
        - MySQL/MariaDB
        - PostgreSQL
        - SQLite
    validations:
      required: true

  - type: dropdown
    id: webserver
    attributes:
      label: Webserver used (when not using Docker)
      options:
        - Apache HTTPD
        - Nginx
        - Not applicable
    validations:
      required: true

  - type: input
    id: db_version
    attributes:
      label: Database version used
      placeholder: e.g., 10.5, 16, 3
    validations:
      required: false

  - type: textarea
    id: error_messages
    attributes:
      label: Error Messages
      description: Paste any relevant error messages or logs.
      render: shell
    validations:
      required: true

  - type: textarea
    id: reproduction_steps
    attributes:
      label: Steps to Reproduce
      description: Provide detailed steps to reliably reproduce the issue.
      placeholder: |
        1. Go to '...'
        2. Click on '...'
        3. Scroll to '...'
        4. See error
    validations:
      required: true

  - type: textarea
    id: expected_behavior
    attributes:
      label: Expected behavior
      description: Describe what you expected to happen.
    validations:
      required: true

  - type: textarea
    id: screenshots
    attributes:
      label: Screenshots
      description: Add screenshots if applicable.
    validations:
      required: false

  - type: textarea
    id: additional_context
    attributes:
      label: Additional context
      description: Add any other relevant information about the issue.
    validations:
      required: false
