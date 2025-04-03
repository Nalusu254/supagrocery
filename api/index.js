module.exports = (req, res) => {
  res.json({
    status: 'ok',
    message: 'Supagrocery API is running',
    version: '1.0.0',
    timestamp: Date.now(),
    path: req.url,
    method: req.method
  });
}; 