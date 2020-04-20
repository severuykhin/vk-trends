import GroupSummaryType from '../types/GroupSummary'

export default (data: any): GroupSummaryType => {
  return {
    comments_count: data.comments_count ? parseInt(data.comments_count) : 0,
    comments_per_post: data.comments_per_post ? parseInt(data.comments_per_post) : 0,
    likes_per_post: data.likes_per_post ? parseInt(data.likes_per_post) : 0,
    posts_count: data.posts_count ? parseInt(data.posts_count) : 0,
    reposts_per_post: data.reposts_per_post ? parseInt(data.reposts_per_post) : 0,
    views_per_post: data.views_per_post ? parseInt(data.views_per_post) : 0
  }  
}