// Detect Node env
export const isNodeEnv = () => typeof window === 'undefined' || typeof document === 'undefined'
