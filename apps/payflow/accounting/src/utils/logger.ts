export const logger = {
  info: (message: string, meta?: any) => {
    console.log(
      JSON.stringify({
        level: "INFO",
        message,
        ...meta,
        timestamp: new Date().toISOString(),
      })
    );
  },

  error: (message: string, meta?: any) => {
    console.error(
      JSON.stringify({
        level: "ERROR",
        message,
        ...meta,
        timestamp: new Date().toISOString(),
      })
    );
  },
};
