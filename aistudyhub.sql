-- Tạo Database theo chuẩn SQL Server
IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = 'aistudyhub')
BEGIN
    CREATE DATABASE [aistudyhub];
END
GO

USE [aistudyhub];
GO

-- 1. Bảng Users (Tài khoản người dùng)
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[users]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[users] (
      [user_id] INT IDENTITY(1,1) PRIMARY KEY,
      [username] NVARCHAR(50) NOT NULL,
      [email] NVARCHAR(100) NOT NULL UNIQUE,
      [password] NVARCHAR(255) NOT NULL,
      [avatar] NVARCHAR(255) DEFAULT 'default_avatar.png',
      [role] NVARCHAR(10) DEFAULT 'user' CONSTRAINT CK_users_role CHECK ([role] IN ('user', 'admin')),
      [status] NVARCHAR(10) DEFAULT 'active' CONSTRAINT CK_users_status CHECK ([status] IN ('active', 'locked')),
      [created_at] DATETIME DEFAULT GETDATE()
    );
END
GO

-- 2. Bảng Documents (Tài liệu)
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[documents]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[documents] (
      [document_id] INT IDENTITY(1,1) PRIMARY KEY,
      [user_id] INT,
      [title] NVARCHAR(255) NOT NULL,
      [file_path] NVARCHAR(255) NOT NULL,
      [file_type] NVARCHAR(10) NOT NULL,
      [status] NVARCHAR(10) DEFAULT 'pending' CONSTRAINT CK_documents_status CHECK ([status] IN ('pending', 'approved', 'rejected')),
      [created_at] DATETIME DEFAULT GETDATE(),
      FOREIGN KEY ([user_id]) REFERENCES [dbo].[users]([user_id]) ON DELETE CASCADE
    );
END
GO

-- 3. Bảng Chat History (Lịch sử chat AI)
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[chat_history]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[chat_history] (
      [chat_id] INT IDENTITY(1,1) PRIMARY KEY,
      [user_id] INT,
      [user_message] NVARCHAR(MAX) NOT NULL,
      [ai_response] NVARCHAR(MAX) NOT NULL,
      [created_at] DATETIME DEFAULT GETDATE(),
      FOREIGN KEY ([user_id]) REFERENCES [dbo].[users]([user_id]) ON DELETE CASCADE
    );
END
GO