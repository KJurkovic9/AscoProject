type Identifier = string | number;

const str = (s?: Identifier) => s?.toString();

export const queryKeys = {
  review: {
    check: (companyId?: Identifier) => ["review", str(companyId)],
  },
  company: {
    all: ["companies"],
    id: (companyId: Identifier) => ["company", str(companyId)],
  },
  project: {
    id: (projectId: Identifier) => ["project", str(projectId)],
  },
  profile: ["profile"],
  loggedIn: ["loggedin"],
} as const;
