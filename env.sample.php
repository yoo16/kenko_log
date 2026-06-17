<?php
// TODO: 自分の環境に合わせて設定を変更
const DB_CONNECTION = 'mysql';
const DB_HOST = '127.0.0.1';
const DB_NAME = 'health_log';
const DB_USER = 'root';
const DB_PASS = '';
const DB_PORT = '3306';
const DB_CHARSET = 'utf8mb4';

// Gemini API の URL
const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/';
// Gemini APIキーをここに入力してください
const GEMINI_API_KEY = '';
// Geminiモデルの指定
const GEMINI_MODEL = 'gemini-2.5-flash-lite';