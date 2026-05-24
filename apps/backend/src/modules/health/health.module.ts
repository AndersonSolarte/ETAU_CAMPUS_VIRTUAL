export const healthModule = {
  status() {
    return {
      status: "ok",
      service: "tau-api",
      timestamp: new Date().toISOString()
    };
  }
};

